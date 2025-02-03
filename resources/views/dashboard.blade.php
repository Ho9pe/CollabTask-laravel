<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if($pendingInvitations = auth()->user()->pendingGroupInvitations)
                @if($pendingInvitations->isNotEmpty())
                    <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-white mb-4">Pending Group Invitations</h3>
                            <div class="space-y-4">
                                @foreach($pendingInvitations as $invitation)
                                    <div class="flex items-center justify-between bg-gray-700 p-4 rounded-lg">
                                        <div>
                                            <p class="text-white">{{ $invitation->group->name }}</p>
                                            <p class="text-sm text-gray-400">Invited by {{ $invitation->inviter->name }}</p>
                                        </div>
                                        <div class="flex gap-4">
                                            <a href="{{ route('groups.invitations.accept', $invitation) }}" 
                                                class="text-green-400 hover:text-green-300">Accept</a>
                                            <a href="{{ route('groups.invitations.reject', $invitation) }}" 
                                                class="text-red-400 hover:text-red-300">Decline</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Tasks -->
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <div class="text-3xl font-bold text-white">{{ $totalTasks }}</div>
                    <div class="text-gray-400 mt-1">Total Tasks</div>
                </div>

                <!-- Completed Tasks -->
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <div class="text-3xl font-bold text-green-400">{{ $completedTasks }}</div>
                    <div class="text-gray-400 mt-1">Completed Tasks</div>
                </div>

                <!-- Pending Tasks -->
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <div class="text-3xl font-bold text-yellow-400">{{ $pendingTasks }}</div>
                    <div class="text-gray-400 mt-1">Pending Tasks</div>
                </div>

                <!-- Overdue Tasks -->
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700 
                    {{ $overdueTasks > 0 ? 'animate-pulse' : '' }}">
                    <div class="text-3xl font-bold text-red-400">{{ $overdueTasks }}</div>
                    <div class="text-gray-400 mt-1">Overdue Tasks</div>
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Priority Distribution</h3>
                    <div class="space-y-4">
                        @foreach(['high', 'medium', 'low'] as $priority)
                            <div>
                                <div class="flex justify-between text-sm text-gray-400 mb-1">
                                    <span>{{ ucfirst($priority) }}</span>
                                    <span>{{ $priorityStats[$priority] ?? 0 }}</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $priority === 'high' ? 'bg-red-500' : ($priority === 'medium' ? 'bg-yellow-500' : 'bg-green-500') }}"
                                        style="width: {{ $totalTasks > 0 ? (($priorityStats[$priority] ?? 0) / $totalTasks) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-white">Recent Tasks</h3>
                            <a href="{{ route('tasks.create') }}" class="text-sm text-blue-400 hover:text-blue-300">
                                Create New Task →
                            </a>
                        </div>
                        @include('partials.task-list', ['tasks' => $recentTasks])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
