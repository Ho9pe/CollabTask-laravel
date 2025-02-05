<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-white">
                    {{ $group->name }}
                </h2>
                <p class="text-sm text-gray-400 mt-1">Created {{ $group->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex items-center gap-4">
                @if($group->user_id === auth()->id())
                    <button onclick="document.getElementById('edit-modal').classList.remove('hidden')" 
                        class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Group
                    </button>
                    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this group?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Group
                        </button>
                    </form>
                @endif
                <button onclick="document.getElementById('invite-modal').classList.remove('hidden')" 
                    class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Invite Member
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Group Info & Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Description Card -->
                <div class="lg:col-span-2 bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-3">About</h3>
                    <p class="text-gray-400">{{ $group->description ?: 'No description provided.' }}</p>
                </div>

                <!-- Quick Stats -->
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                            <span class="text-gray-300">Total Tasks</span>
                            <span class="text-purple-400 font-medium">{{ $group->tasks->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                            <span class="text-gray-300">Completed Tasks</span>
                            <span class="text-green-400 font-medium">{{ $group->tasks->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                            <span class="text-gray-300">Members</span>
                            <span class="text-blue-400 font-medium">{{ $group->members->count() + 1 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Section -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-semibold text-white mb-4">Members</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Owner Card -->
                    <div class="bg-gray-700 p-4 rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-green-400 text-sm font-medium">Owner</span>
                                <div class="text-white font-medium mt-1">{{ $group->user->name }}</div>
                                <div class="text-gray-400 text-sm mt-1">{{ $group->user->email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-400">Tasks Created</div>
                                <div class="text-2xl font-semibold text-white mt-1">
                                    {{ $group->tasks()->where('user_id', $group->user_id)->count() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Members Cards -->
                    @foreach($group->members as $member)
                        <div class="bg-gray-700 p-4 rounded-lg border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-blue-400 text-sm font-medium">Member</span>
                                    <div class="text-white font-medium mt-1">{{ $member->name }}</div>
                                    <div class="text-gray-400 text-sm mt-1">{{ $member->email }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-400">Assigned Tasks</div>
                                    <div class="text-2xl font-semibold text-white mt-1">
                                        {{ $group->tasks()->where('assigned_to', $member->id)->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-white">Group Tasks</h3>
                        <a href="{{ route('tasks.create', ['group_id' => $group->id]) }}" 
                            class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Task
                        </a>
                    </div>

                    <!-- Task Categories -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Owner's Tasks Section -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-white font-medium flex items-center gap-2">
                                    <span class="text-green-400">●</span>
                                    {{ $group->user->name }}'s Tasks
                                </h4>
                                <span class="text-sm text-gray-400">Owner</span>
                            </div>
                            @include('partials.task-list', [
                                'tasks' => $tasks->where('assigned_to', $group->user_id)
                                    ->merge($tasks->whereNull('assigned_to')->where('user_id', $group->user_id))
                            ])
                        </div>

                        <!-- Each Member's Tasks Section -->
                        @foreach($group->members as $member)
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-white font-medium flex items-center gap-2">
                                        <span class="text-blue-400">●</span>
                                        {{ $member->name }}'s Tasks
                                    </h4>
                                    <span class="text-sm text-gray-400">Member</span>
                                </div>
                                @include('partials.task-list', [
                                    'tasks' => $tasks->where('assigned_to', $member->id)
                                        ->merge($tasks->whereNull('assigned_to')->where('user_id', $member->id))
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{ $tasks->links() }}
        </div>
    </div>

    <!-- Invite Modal -->
    <div id="invite-modal" class="hidden fixed inset-0 bg-gray-900/75 flex items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg w-full max-w-md border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Invite Member</h3>
            <form action="{{ route('groups.invite', $group) }}" method="POST">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-300">Email Address</label>
                    <input type="email" name="email" required
                        class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <button type="button" 
                        onclick="document.getElementById('invite-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-gray-900/75 flex items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg w-full max-w-md border border-gray-700">
            <h3 class="text-lg font-semibold text-white mb-4">Edit Group</h3>
            <form action="{{ route('groups.update', $group) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Group Name</label>
                        <input type="text" name="name" value="{{ $group->name }}" required
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Description</label>
                        <textarea name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">{{ $group->description }}</textarea>
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <button type="button" 
                        onclick="document.getElementById('edit-modal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 