<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Upcoming tasks</h3>
                    <a href="{{ route('lists.index') }}" class="text-sm text-blue-600 hover:underline">View all lists</a>
                </div>

                @if ($upcomingTasks->isEmpty())
                    <p class="text-gray-500">No upcoming tasks. <a href="{{ route('lists.index') }}" class="text-blue-600 hover:underline">Create a list</a> to get started.</p>
                @else
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b text-sm text-gray-500">
                                <th class="text-left py-2">Task</th>
                                <th class="text-left py-2">List</th>
                                <th class="text-left py-2">Deadline</th>
                                <th class="text-left py-2">Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upcomingTasks as $task)
                                <tr class="border-b">
                                    <td class="py-2">{{ $task->title }}</td>
                                    <td class="py-2">{{ $task->list->name }}</td>
                                    <td class="py-2">{{ $task->deadline?->format('M j, Y') ?? '—' }}</td>
                                    <td class="py-2">
                                        <span @class([
                                            'px-2 py-0.5 rounded text-xs font-medium',
                                            'bg-red-100 text-red-700' => $task->priority === 'high',
                                            'bg-yellow-100 text-yellow-700' => $task->priority === 'medium',
                                            'bg-gray-100 text-gray-700' => $task->priority === 'low',
                                        ])>
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">Manage users</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>