<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('assigned_to', auth()->id());
        })
        ->with(['group', 'assignedUser', 'user'])
        ->orderByRaw("CASE 
            WHEN status = 'pending' AND due_date < CURRENT_DATE THEN 1
            WHEN status = 'pending' AND priority = 'high' THEN 2
            WHEN status = 'pending' AND priority = 'medium' THEN 3
            WHEN status = 'pending' AND priority = 'low' THEN 4
            WHEN status = 'completed' THEN 5
            ELSE 6 END")
        ->orderBy('due_date', 'asc')
        ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        // Get both owned and member groups
        $ownedGroups = auth()->user()->ownedGroups()->with('members')->get();
        $memberGroups = auth()->user()->groups()->with('members')->get();
        $groups = $ownedGroups->merge($memberGroups);
        
        return view('tasks.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'group_id' => 'nullable|exists:groups,id',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($validated['group_id']) {
            $group = Group::findOrFail($validated['group_id']);
            
            // Check if user can create tasks in this group
            if ($group->user_id !== auth()->id() && !$group->members->contains(auth()->id())) {
                abort(403, 'You do not have permission to create tasks in this group.');
            }

            // If assigned_to is set, verify they're a group member
            if (!empty($validated['assigned_to'])) {
                $isValidAssignee = $validated['assigned_to'] == $group->user_id 
                    || $group->members->contains($validated['assigned_to']);
                
                if (!$isValidAssignee) {
                    abort(403, 'Cannot assign task to a user who is not a member of the group.');
                }
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $task = Task::create($validated);

        $redirectRoute = $validated['group_id'] 
            ? route('groups.show', $validated['group_id'])
            : route('tasks.index');

        return redirect($redirectRoute)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }
        
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function toggleStatus(Task $task)
    {
        if ($task->user_id !== auth()->id() && $task->assigned_to !== auth()->id()) {
            abort(403);
        }
        
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null
        ]);

        return redirect()->back()->with('success', 'Task status updated successfully.');
    }
} 