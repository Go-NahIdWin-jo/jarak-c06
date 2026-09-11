<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tasks — {{ $list->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="mb-4"><a href="{{ route('lists.index') }}" class="text-blue-600 hover:underline">Back to my lists</a></p>

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        <ul class="mb-0 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="text-lg font-semibold mb-2">Add a task</h3>
                <form action="{{ route('tasks.store', $list) }}" method="POST" class="flex gap-2 mb-8 items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm text-gray-600">Title</label>
                        <input type="text" name="title" class="w-full border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Deadline</label>
                        <input type="date" name="deadline" class="border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">Priority</label>
                        <select name="priority" class="border-gray-300 rounded">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add</button>
                </form>

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b text-sm text-gray-500">
                            <th class="text-left py-2">Done</th>
                            <th class="text-left py-2">Title</th>
                            <th class="text-left py-2">Deadline</th>
                            <th class="text-left py-2">Priority</th>
                            <th class="text-right py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr class="border-b {{ $task->is_completed ? 'opacity-50' : '' }}">
                                <td class="py-2">
                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="checkbox" onchange="this.form.submit()" {{ $task->is_completed ? 'checked' : '' }}>
                                    </form>
                                </td>
                                <td class="py-2 {{ $task->is_completed ? 'line-through' : '' }}">{{ $task->title }}</td>
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
                                <td class="py-2 text-right">
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>