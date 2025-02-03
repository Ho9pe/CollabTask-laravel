<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Create Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Title</label>
                        <input type="text" name="title" required
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300">Description</label>
                        <textarea name="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Priority</label>
                            <select name="priority" required
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300">Due Date</label>
                            <input type="date" name="due_date"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300">Group</label>
                        <select name="group_id" onchange="toggleAssigneeSelect(this.value)"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                            <option value="">Personal Task</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" 
                                    {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="assignee-select" class="hidden">
                        <label class="block text-sm font-medium text-gray-300">Assign To</label>
                        <select name="assigned_to"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white">
                            <option value="{{ auth()->id() }}">Self</option>
                            @foreach($groups as $group)
                                @if(request('group_id') == $group->id)
                                    <!-- Show group owner -->
                                    @if($group->user_id !== auth()->id())
                                        <option value="{{ $group->user_id }}" data-group="{{ $group->id }}">
                                            {{ $group->user->name }}
                                        </option>
                                    @endif
                                    
                                    <!-- Show group members -->
                                    @foreach($group->members as $member)
                                        @if($member->id !== auth()->id())
                                            <option value="{{ $member->id }}" data-group="{{ $group->id }}">
                                                {{ $member->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAssigneeSelect(groupId) {
            const assigneeSelect = document.getElementById('assignee-select');
            const options = assigneeSelect.querySelectorAll('option[data-group]');
            
            if (groupId) {
                assigneeSelect.classList.remove('hidden');
                options.forEach(option => {
                    option.style.display = option.dataset.group === groupId ? '' : 'none';
                });
            } else {
                assigneeSelect.classList.add('hidden');
            }
        }

        // Initialize on page load if group is pre-selected
        document.addEventListener('DOMContentLoaded', () => {
            const groupSelect = document.querySelector('select[name="group_id"]');
            if (groupSelect.value) {
                toggleAssigneeSelect(groupSelect.value);
            }
        });
    </script>
</x-app-layout> 