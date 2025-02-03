<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-white">
                {{ __('Task Groups') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($groups as $group)
                    <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-white">{{ $group->name }}</h3>
                            <p class="mt-1 text-gray-400">{{ $group->description }}</p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-sm text-gray-500">{{ $group->tasks_count }} tasks</span>
                                <a href="{{ route('groups.show', $group) }}" 
                                    class="text-blue-400 hover:text-blue-300">View Tasks →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <p class="text-gray-400 text-center">No groups created yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout> 