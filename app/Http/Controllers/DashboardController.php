<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Basic Stats
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->where('status', 'completed')->count();
        $pendingTasks = $user->tasks()->where('status', 'pending')->count();
        $overdueTasks = $user->tasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        // Priority Distribution
        $priorityStats = $user->tasks()
            ->where('status', '!=', 'completed')
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Recent Tasks with more context
        $recentTasks = $user->tasks()
            ->with(['group'])
            ->latest()
            ->take(5)
            ->get();

        // Completion Rate
        $completionRate = $totalTasks > 0 
            ? round(($completedTasks / $totalTasks) * 100) 
            : 0;

        return view('dashboard', compact(
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks',
            'recentTasks',
            'priorityStats',
            'completionRate'
        ));
    }
} 