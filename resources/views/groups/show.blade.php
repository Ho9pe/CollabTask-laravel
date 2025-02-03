<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ $group->name }}
            </h2>
            <div class="flex gap-4">
                @if($group->user_id === auth()->id())
                    <button onclick="document.getElementById('edit-modal').classList.remove('hidden')" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Edit Group
                    </button>
                    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this group?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                            Delete Group
                        </button>
                    </form>
                @endif
                <button onclick="document.getElementById('invite-modal').classList.remove('hidden')" 
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Invite Member
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Group Info -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                <p class="text-gray-400">{{ $group->description }}</p>
                <div class="mt-4">
                    <h3 class="text-lg font-semibold text-white mb-4">Members</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Owner Card -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-green-400 text-sm">Owner</span>
                                    <div class="text-white font-medium">{{ $group->user->name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-400">Tasks Created</div>
                                    <div class="text-white">{{ $group->tasks()->where('user_id', $group->user_id)->count() }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Members Cards -->
                        @foreach($group->members as $member)
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div class="text-white font-medium">{{ $member->name }}</div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-400">Assigned Tasks</div>
                                        <div class="text-white">{{ $group->tasks()->where('assigned_to', $member->id)->count() }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tasks -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-white">Group Tasks</h3>
                        <a href="{{ route('tasks.create', ['group_id' => $group->id]) }}" 
                            class="text-blue-400 hover:text-blue-300">
                            Create Task →
                        </a>
                    </div>

                    <!-- Task Categories -->
                    <div class="grid grid-cols-1 gap-6 mb-6">
                        <!-- My Tasks Section -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-white font-medium mb-3">My Tasks</h4>
                            @php
                                $myTasks = $tasks->filter(fn($task) => 
                                    $task->assigned_to === auth()->id() || 
                                    ($task->user_id === auth()->id() && $task->assigned_to === null)
                                );
                            @endphp
                            @include('partials.task-list', ['tasks' => $myTasks])
                        </div>

                        <!-- Owner's Tasks Section -->
                        @if($group->user_id !== auth()->id())
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-white font-medium mb-3">{{ $group->user->name }}'s Tasks</h4>
                                @php
                                    $ownerTasks = $tasks->filter(fn($task) => 
                                        $task->assigned_to === $group->user_id || 
                                        ($task->user_id === $group->user_id && $task->assigned_to === null)
                                    );
                                @endphp
                                @include('partials.task-list', ['tasks' => $ownerTasks])
                            </div>
                        @endif

                        <!-- Other Members' Tasks -->
                        @foreach($group->members as $member)
                            @if($member->id !== auth()->id())
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-white font-medium mb-3">{{ $member->name }}'s Tasks</h4>
                                    @php
                                        $memberTasks = $tasks->filter(fn($task) => 
                                            $task->assigned_to === $member->id || 
                                            ($task->user_id === $member->id && $task->assigned_to === null)
                                        );
                                    @endphp
                                    @include('partials.task-list', ['tasks' => $memberTasks])
                                </div>
                            @endif
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