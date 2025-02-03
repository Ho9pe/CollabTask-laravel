<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class GroupInvitationController extends Controller
{
    public function invite(Request $request, Group $group)
    {
        if ($group->user_id !== auth()->id() && !$group->members->contains(auth()->id())) {
            abort(403);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Check if user is already a member
        $user = User::where('email', $validated['email'])->first();
        if ($group->members->contains($user->id) || $group->user_id === $user->id) {
            return back()->with('error', 'User is already a member of this group.');
        }

        // Check for pending invitation
        $pendingInvitation = GroupInvitation::where([
            'group_id' => $group->id,
            'email' => $validated['email'],
            'status' => 'pending'
        ])->first();

        if ($pendingInvitation) {
            return back()->with('error', 'An invitation is already pending for this user.');
        }

        // Create invitation
        GroupInvitation::create([
            'group_id' => $group->id,
            'email' => $validated['email'],
            'invited_by' => auth()->id()
        ]);

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function accept(GroupInvitation $invitation)
    {
        if ($invitation->email !== auth()->user()->email || $invitation->status !== 'pending') {
            abort(403);
        }

        $invitation->update(['status' => 'accepted']);
        $invitation->group->members()->attach(auth()->id());

        return redirect()->route('groups.show', $invitation->group)
            ->with('success', 'You have joined the group successfully.');
    }

    public function reject(GroupInvitation $invitation)
    {
        if ($invitation->email !== auth()->user()->email || $invitation->status !== 'pending') {
            abort(403);
        }

        $invitation->update(['status' => 'rejected']);

        return redirect()->route('dashboard')
            ->with('success', 'Invitation rejected successfully.');
    }
} 