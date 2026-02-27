<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show invitations for a colocation
     */
    public function index(Colocation $colocation)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $invitations = $colocation->invitations()->latest()->paginate(15);
        return view('invitations.index', compact('colocation', 'invitations'));
    }

    /**
     * Show create invitation form
     */
    public function create(Colocation $colocation)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('invitations.create', compact('colocation'));
    }

    /**
     * Send invitation
     */
    public function store(Request $request, Colocation $colocation)
    {
        // Check authorization - owner only
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Check if user already exists and has active colocation
        $user = User::where('email', $validated['email'])->first();
        if ($user && $user->colocations()
            ->where('status', 'active')
            ->wherePivot('left_at', null)
            ->exists()) {
            return redirect()->back()->withErrors(['email' => 'Cet utilisateur a déjà une colocation active.']);
        }

        // Delete existing pending invitations
        Invitation::where('colocation_id', $colocation->id)
            ->where('email', $validated['email'])
            ->where('status', 'pending')
            ->delete();

        $token = Str::random(32);
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'invited_by' => Auth::id(),
            'email' => $validated['email'],
            'token' => $token,
            'expires_at' => now()->addDays(7),
        ]);

        // Send email
        try {
            Mail::send('emails.invitation', [
                'colocation' => $colocation,
                'invitedBy' => Auth::user(),
                'token' => $token,
                'email' => $validated['email'],
            ], function ($message) use ($validated, $colocation) {
                $message->to($validated['email'])
                    ->subject("Invitation à rejoindre {$colocation->nom}");
            });
        } catch (\Exception $e) {
            // Log::error('Failed to send invitation email: ' . $e->getMessage());
        }

        return redirect()
            ->route('invitations.index', $colocation)
            ->with('status', 'Invitation envoyée à ' . $validated['email']);
    }

    /**
     * Accept invitation
     */
    public function accept(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|exists:invitations,token',
            'email' => 'required|email',
        ]);

        $invitation = Invitation::where('token', $validated['token'])
            ->where('email', $validated['email'])
            ->firstOrFail();

        // Check if expired
        if ($invitation->isExpired()) {
            return redirect()->route('dashboard')->with('error', 'Cette invitation a expiré.');
        }

        // Check if already accepted/declined
        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Cette invitation a déjà été traitée.');
        }

        $user = Auth::user();

        // Check if authenticated user email matches invitation email
        if ($user->email !== $validated['email']) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Veuillez vous connecter avec l\'email associé à l\'invitation.');
        }

        // Check if user has active colocation
        if ($user->colocations()
            ->where('status', 'active')
            ->wherePivot('left_at', null)
            ->exists()) {
            return redirect()->route('dashboard')->withErrors(['Vous avez déjà une colocation active.']);
        }

        // Attach user to colocation
        $invitation->colocation->member()->attach($user->id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()
            ->route('colocations.show', $invitation->colocation)
            ->with('status', 'Invitation acceptée!');
    }

    /**
     * Decline invitation
     */
    public function decline(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|exists:invitations,token',
            'email' => 'required|email',
        ]);

        $invitation = Invitation::where('token', $validated['token'])
            ->where('email', $validated['email'])
            ->firstOrFail();

        // Check if expired
        if ($invitation->isExpired()) {
            return redirect()->route('dashboard')->with('error', 'Cette invitation a expiré.');
        }

        // Check if already accepted/declined
        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Cette invitation a déjà été traitée.');
        }

        $invitation->update(['status' => 'declined']);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Invitation refusée.');
    }

    /**
     * Delete invitation (owner only)
     */
    public function destroy(Colocation $colocation, Invitation $invitation)
    {
        // Check authorization
        if ($colocation->owner_id !== Auth::id() || $invitation->colocation_id !== $colocation->id) {
            abort(403);
        }

        $invitation->delete();

        return redirect()
            ->route('invitations.index', $colocation)
            ->with('status', 'Invitation supprimée.');
    }
}
