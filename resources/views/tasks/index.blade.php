<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ __('Tasks') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Overdue Tasks -->
            @php
                $overdueTasks = $tasks->where('status', '!=', 'completed')
                    ->filter(fn($task) => $task->due_date && $task->due_date < now());
            @endphp
            @if($overdueTasks->isNotEmpty())
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-red-800/30">
                    <div class="p-6 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-red-400 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Overdue Tasks
                        </h3>
                        @include('partials.task-list', ['tasks' => $overdueTasks])
                    </div>
                </div>
            @endif

            <!-- Active Tasks -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Active Tasks</h3>
                    @include('partials.task-list', [
                        'tasks' => $tasks->where('status', '!=', 'completed')
                            ->filter(fn($task) => !$task->due_date || $task->due_date >= now())
                    ])
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Completed Tasks</h3>
                    @include('partials.task-list', ['tasks' => $tasks->where('status', 'completed')])
                </div>
            </div>

            {{ $tasks->links() }}
        </div>
    </div>
</x-app-layout> 