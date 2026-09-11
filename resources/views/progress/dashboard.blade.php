<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Back to Dashboard</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Progress Tracking
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- My Lists -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">My Lists</h3>
                @if($myLists->isEmpty())
                    <p class="text-gray-500 text-sm">You don't have any lists yet. <a href="{{ route('lists.index') }}" class="text-blue-600 hover:underline">Create one</a>.</p>
                @else
                    <div class="space-y-4">
                        @foreach($myLists as $list)
                            @php $progress = $list->progressPercentage(); @endphp
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-800">{{ $list->name }}</h4>
                                    <span class="text-sm text-gray-500">
                                        {{ $list->tasks->where('is_completed', true)->count() }} / {{ $list->tasks->count() }} tasks
                                    </span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar"
                                         style="width: {{ $progress }}%; background-color: {{ $progress < 30 ? '#ef4444' : ($progress < 70 ? '#f59e0b' : '#22c55e') }};">
                                        {{ $progress }}%
                                    </div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('collaborators.index', $list->id) }}" class="text-xs text-blue-600 hover:underline">Manage Collaborators</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('tasks.index', $list->id) }}" class="text-xs text-blue-600 hover:underline">View Tasks</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Shared With Me -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Shared With Me</h3>
                @if($sharedLists->isEmpty())
                    <p class="text-gray-500 text-sm">No lists have been shared with you.</p>
                @else
                    <div class="space-y-4">
                        @foreach($sharedLists as $list)
                            @php $progress = $list->progressPercentage(); @endphp
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ $list->name }}</h4>
                                        <p class="text-xs text-gray-400">Owner: {{ $list->owner->name }}</p>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $list->tasks->where('is_completed', true)->count() }} / {{ $list->tasks->count() }} tasks
                                    </span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar"
                                         style="width: {{ $progress }}%; background-color: {{ $progress < 30 ? '#ef4444' : ($progress < 70 ? '#f59e0b' : '#22c55e') }};">
                                        {{ $progress }}%
                                    </div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('collaborators.index', $list->id) }}" class="text-xs text-blue-600 hover:underline">View Collaborators</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('tasks.index', $list->id) }}" class="text-xs text-blue-600 hover:underline">View Tasks</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
