<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get both owned and member groups
        $activeGroups = Group::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereHas('members', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        })
        ->where('status', 'active')
        ->withCount('tasks')
        ->get();

        \Log::info('Active Groups:', ['count' => $activeGroups->count()]);
        \Log::info('First Group Status:', ['status' => $activeGroups->first()?->status]);

        $archivedGroups = Group::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereHas('members', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        })
        ->where('status', 'completed')
        ->withCount('tasks')
        ->get();

        return view('groups.index', compact('activeGroups', 'archivedGroups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';
        
        $group = Group::create($validated);

        // Add debug logging
        \Log::info('Group created:', ['group' => $group->toArray()]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        // Allow both owner and members to view the group
        if ($group->user_id !== auth()->id() && !$group->members->contains(auth()->id())) {
            abort(403);
        }

        $tasks = $group->tasks()
            ->orderByRaw("CASE 
                WHEN status = 'pending' AND due_date < CURRENT_DATE THEN 1
                WHEN status = 'pending' AND priority = 'high' THEN 2
                WHEN status = 'pending' AND priority = 'medium' THEN 3
                WHEN status = 'pending' AND priority = 'low' THEN 4
                WHEN status = 'completed' THEN 5
                ELSE 6 END")
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        return view('groups.show', compact('group', 'tasks'));
    }

    public function update(Request $request, Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $group->update($validated);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully.');
    }

    public function completeAll(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }
        $group->tasks()->update(['status' => 'completed']);
        return redirect()->route('groups.show', $group)
            ->with('success', 'All tasks in the group have been marked as completed.');
    }

    public function toggleStatus(Group $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $group->status = $group->status === 'active' ? 'completed' : 'active';
        $group->save();

        return back()->with('success', 'Group status updated successfully.');
    }
} 