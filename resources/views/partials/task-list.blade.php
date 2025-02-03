@forelse($tasks as $task)
    <div class="bg-gray-800 p-4 rounded-lg mb-3 last:mb-0 border-l-4 transition-all hover:translate-x-1 group
        {{ $task->status === 'completed' ? 'border-green-500 opacity-75' : 
            ($task->priority === 'high' ? 'border-red-500' : 
            ($task->priority === 'medium' ? 'border-yellow-500' : 'border-blue-500')) }}
        {{ $task->status === 'completed' ? 'bg-gray-800/50' : 
            ($task->priority === 'high' ? 'bg-red-900/10' : 
            ($task->priority === 'medium' ? 'bg-yellow-900/10' : 'bg-blue-900/10')) }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 flex-1">
                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="flex-shrink-0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors
                        {{ $task->status === 'completed' ? 
                            'border-green-500 bg-green-500/20 hover:bg-green-500/30' : 
                            'border-gray-500 hover:border-gray-400' }}">
                        @if($task->status === 'completed')
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                        @endif
                    </button>
                </form>
                <div class="min-w-0 flex-1">
                    <div class="text-white truncate {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">
                        {{ $task->title }}
                    </div>
                    @if($task->description)
                        <p class="text-sm text-gray-400 line-clamp-2">{{ $task->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-col items-end gap-2 ml-4">
                <div class="flex items-center gap-2">
                    @if($task->user_id === auth()->id())
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('tasks.edit', $task) }}" 
                                class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    onclick="return confirm('Are you sure you want to delete this task?')"
                                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-500/20 rounded-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : 
                           ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 
                           'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                    @if($task->assigned_to)
                        @if($task->assigned_to === auth()->id())
                            <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full font-medium">
                                Assigned to me
                            </span>
                        @elseif($task->assignedUser)
                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full font-medium">
                                For: {{ $task->assignedUser->name }}
                            </span>
                        @endif
                    @endif
                </div>
                @if($task->due_date)
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                        {{ $task->due_date < now() && $task->status !== 'completed' ? 
                           'bg-red-100 text-red-800' : 
                           'bg-gray-700 text-gray-300' }}">
                        Due: {{ $task->due_date->format('M d, Y') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@empty
    <p class="text-gray-400 text-center py-4">No tasks found.</p>
@endforelse 