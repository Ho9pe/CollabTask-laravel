<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ __('Task Groups') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Active Groups -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Active Groups</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($activeGroups as $group)
                        <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 hover:border-purple-500/20 transition-all group">
                            <a href="{{ route('groups.show', $group) }}" class="block p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white group-hover:text-purple-300 transition-colors">{{ $group->name }}</h3>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $group->tasks_count > 0 ? 'bg-purple-500/20 text-purple-300' : 'bg-gray-700 text-gray-400' }}">
                                            {{ $group->tasks_count }} {{ Str::plural('task', $group->tasks_count) }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300">
                                            {{ $group->members->count() + 1 }} {{ Str::plural('member', $group->members->count() + 1) }}
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-1 text-gray-400 text-sm line-clamp-2">{{ $group->description ?: 'No description provided.' }}</p>
                            </a>

                            <div class="px-6 py-4 border-t border-gray-700 bg-gray-800/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <!-- Archive Button -->
                                        <form action="{{ route('groups.toggle-status', $group) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                class="text-green-400 hover:text-green-300 text-sm flex items-center gap-1 p-2 rounded-lg hover:bg-green-400/10 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                                </svg>
                                                <span>Archive</span>
                                            </button>
                                        </form>

                                        <!-- Delete Button -->
                                        <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                onclick="return confirm('Are you sure you want to delete this group?')"
                                                class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1 p-2 rounded-lg hover:bg-red-500/10 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <!-- Edit Button -->
                                        <button onclick="document.getElementById('edit-modal-{{ $group->id }}').classList.remove('hidden')"
                                            class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1 p-2 rounded-lg hover:bg-blue-500/10 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </button>

                                        <!-- Invite Button -->
                                        <button onclick="document.getElementById('invite-modal-{{ $group->id }}').classList.remove('hidden')"
                                            class="text-purple-400 hover:text-purple-300 text-sm flex items-center gap-1 p-2 rounded-lg hover:bg-purple-500/10 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            <span>Invite</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-400">No active groups.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Archived Groups -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Archived Groups</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($archivedGroups as $group)
                        <div class="bg-gray-800/50 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 group">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-400">{{ $group->name }}</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-700 text-gray-400">
                                        {{ $group->tasks_count }} {{ Str::plural('task', $group->tasks_count) }}
                                    </span>
                                </div>
                                <p class="mt-1 text-gray-500 text-sm line-clamp-2 mb-4">{{ $group->description ?: 'No description provided.' }}</p>
                                
                                <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-700">
                                    <form action="{{ route('groups.toggle-status', $group) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                            class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span>Unarchive</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-400">No archived groups.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Create New Group Section -->
            <div class="bg-gray-800/50 overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-700 hover:border-blue-500/50 transition-all group">
                <a href="{{ route('groups.create') }}" class="p-8 flex items-center justify-center gap-6">
                    <div class="w-16 h-16 rounded-full bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500/20 transition-colors">
                        <svg class="w-8 h-8 text-blue-400 group-hover:text-blue-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-blue-400 group-hover:text-blue-300 transition-colors">Create New Group</h3>
                        <p class="mt-2 text-sm text-gray-400">Start collaborating with your team by creating a new group</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @foreach($activeGroups as $group)
        <!-- Edit Modal for each group -->
        <div id="edit-modal-{{ $group->id }}" class="hidden fixed inset-0 bg-gray-900/75 flex items-center justify-center">
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
                            onclick="document.getElementById('edit-modal-{{ $group->id }}').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Group
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invite Modal for each group -->
        <div id="invite-modal-{{ $group->id }}" class="hidden fixed inset-0 bg-gray-900/75 flex items-center justify-center">
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
                            onclick="document.getElementById('invite-modal-{{ $group->id }}').classList.add('hidden')"
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
    @endforeach
</x-app-layout> 