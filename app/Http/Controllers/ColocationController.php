<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use \App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColocationController extends Controller
{
    

    
     
     
    public function index()
    {
        $user = Auth::user();
        $colocations = $user->colocations()
            ->where('status', 'active')
            ->wherePivot('left_at', null)
            ->get();

        return view('colocations.index', compact('colocations'));
    }

    
     //  creating a new colocation.
     
    public function create()
    {
        return view('colocations.create');
    }

    
     //  Store colocation
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();

        // USER HAVE ONE COLOCATION ACTIVE 
        $exists = $user->colocations()
            ->where('status', 'active')
            ->wherePivot('left_at', null)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['Vous avez déjà une colocation active.']);
        }

        $colocation = Colocation::create([
            'nom' => $request->input('name'),
            'owner_id' => $user->id,
        ]);

        // Add owner  as member in colocation
        $colocation->member()->attach($user->id, [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation);
    }

    
     // show all member in  colocation.
     
    public function show(Colocation $colocation)
    {
        $colocation->load('member');
        return view('colocations.show', compact('colocation'));
    }

    // virifay member after join 
     
     
    public function join(Colocation $colocation)
    {
        $user = Auth::user();

        if ($colocation->member()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('status', 'Vous êtes déjà membre.');
        }

        $exists = $user->colocations()
            ->where('status', 'active')
            ->wherePivot('left_at', null)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['Vous avez déjà une colocation active.']);
        }

        $colocation->member()->attach($user->id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation);
    }

    
      // verfiy befor left 
     
    public function leave(Colocation $colocation)
    {
        $user = Auth::user();

        if (! $colocation->member()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->withErrors(['Vous n’êtes pas membre de cette colocation.']);
        }

        $colocation->member()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        // reputation 
        $balance = $colocation->balanceForUser($user->id);
        if ($balance < -1) {
            $user->reputation -= 1;
        } else {
            $user->reputation += 1;
        }
        $user->save();

        return redirect()->route('colocations.index')->with('status', 'Vous avez quitté la colocation.');
    }

    
      // Only owner can cancel colocation 
     
    public function cancel(Colocation $colocation)
    {
        $user = Auth::user();

        if ($colocation->owner_id !== $user->id) {
            abort(403);
        }

        // Mark all members as left and change  reputation

        foreach ($colocation->getActiveMembers() as $member) {
            $colocation->member()->updateExistingPivot($member->id, ['left_at' => now()]);
            $balance = $colocation->balanceForUser($member->id);
            if ($balance < -1) {
                $member->reputation -= 1;
            } else {
                $member->reputation += 1;
            }
            $member->save();
        }

        $colocation->status = 'cancelled';
        $colocation->save();

        return redirect()->route('colocations.index')->with('status', 'Colocation annulée.');
    }

    
     // Remove  member
     
    public function removeMember(Colocation $colocation, Request $request)
    {
        $user = Auth::user();

        if ($colocation->owner_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $validated['user_id'];

        // CanT remove owner

        if ($userId === $colocation->owner_id) {
            return redirect()->back()->withErrors(['Impossible de retirer le owner.']);
        }

        $member = $colocation->member()->find($userId);
        if (!$member) {
            return redirect()->back()->withErrors(['Membre non trouvé.']);
        }

        //  member as left
        $colocation->member()->updateExistingPivot($userId, ['left_at' => now()]);

        //reputation 

        $memberUser = find($userId);
        $balance = $colocation->balanceForUser($userId);
        if ($balance < -1) {
            $memberUser->reputation -= 1;
        } else {
            $memberUser->reputation += 1;
        }
        $memberUser->save();

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Membre retiré.');
    }

    
     // Update colocation 
     
    public function update(Colocation $colocation, Request $request)
    {
        $user = Auth::user();

        if ($colocation->owner_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $colocation->update(['nom' => $validated['name']]);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('status', 'Colocation mise à jour.');
    }

    
     //Edit colocation form (owner only)
     
    public function edit(Colocation $colocation)
    {
        $user = Auth::user();

        if ($colocation->owner_id !== $user->id) {
            abort(403);
        }

        return view('colocations.edit', compact('colocation'));
    }

    // DELETE

    public function destroy(Colocation $colocation)
    {
        $user = Auth::user();

        if ($colocation->owner_id !== $user->id) {
            abort(403);
        }

        $colocation->delete();

        return redirect()
            ->route('colocations.index')
            ->with('status', 'Colocation supprimée.');
    }
}

