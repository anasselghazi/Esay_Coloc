<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColocationController extends Controller
{
    public function __construct()
    {
        // all actions require authentication
        $this->middleware('auth');
    }

    /**
     * Display a listing of the authenticated user’s active colocations.
     */
    public function index()
    {
        $user = Auth::user();
        $colocations = $user->colocations()
            ->where('status', 'active')
            ->wherePivot('left_at', null)
            ->get();

        return view('colocations.index', compact('colocations'));
    }

    /**
     * Show the form for creating a new colocation.
     */
    public function create()
    {
        return view('colocations.create');
    }

    /**
     * Store a newly created colocation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();

        // a user may only have one active colocation
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

        // owner is automatically attached as member
        $colocation->member()->attach($user->id, [
            'role' => 'owner',
            'joined_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation);
    }

    /**
     * Display the specified colocation.
     */
    public function show(Colocation $colocation)
    {
        $colocation->load('member');
        return view('colocations.show', compact('colocation'));
    }

    /**
     * Join the authenticated user to the given colocation.
     */
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

    /**
     * Mark the authenticated user as having left the colocation.
     */
    public function leave(Colocation $colocation)
    {
        $user = Auth::user();

        if (! $colocation->member()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->withErrors(['Vous n’êtes pas membre de cette colocation.']);
        }

        $colocation->member()->updateExistingPivot($user->id, [
            'left_at' => now(),
        ]);

        return redirect()->route('colocations.index')->with('status', 'Vous avez quitté la colocation.');
    }

    /**
     * Cancel the colocation (owner only).
     */
    public function cancel(Colocation $colocation)
    {
        $user = Auth::user();

        if ($colocation->owner_id !== $user->id) {
            abort(403);
        }

        $colocation->status = 'cancelled';
        $colocation->save();

        return redirect()->route('colocations.index')->with('status', 'Colocation annulée.');
    }
}

