<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Lists
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="mb-4"><a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">Back to dashboard</a></p>

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

                <h3 class="text-lg font-semibold mb-2">Lists you own</h3>
                @if ($ownedLists->isEmpty())
                    <p class="text-gray-500 mb-6">You don't own any lists yet — create one below to start adding tasks.</p>
                @else
                    <table class="w-full mb-8 border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Name</th>
                                <th class="text-right py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ownedLists as $list)
                                <tr class="border-b">
                                    <td class="py-2">
                                        <a href="{{ route('tasks.index', $list) }}" class="font-medium text-blue-600 hover:underline">{{ $list->name }}</a>
                                    </td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('lists.edit', $list) }}" class="text-sm text-gray-600 hover:underline">Rename</a>
                                        <form action="{{ route('lists.destroy', $list) }}" method="POST" class="inline" onsubmit="return confirm('Delete this list?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <h3 class="text-lg font-semibold mb-2">Lists you've joined</h3>
                @if ($joinedLists->isEmpty())
                    <p class="text-gray-500 mb-8">You haven't joined any lists yet.</p>
                @else
                    <table class="w-full mb-8 border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Name</th>
                                <th class="text-left py-2">Owner</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($joinedLists as $list)
                                <tr class="border-b">
                                    <td class="py-2">
                                        <a href="{{ route('tasks.index', $list) }}" class="font-medium text-blue-600 hover:underline">{{ $list->name }}</a>
                                    </td>
                                    <td class="py-2">{{ $list->owner->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <details class="mt-4">
                    <summary class="cursor-pointer text-sm text-gray-500 hover:text-gray-700">+ Create a new list</summary>
                    <form action="{{ route('lists.store') }}" method="POST" class="flex gap-2 mt-3">
                        @csrf
                        <input type="text" name="name" class="border-gray-300 rounded flex-1" placeholder="List name" value="{{ old('name') }}" required>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create</button>
                    </form>
                </details>

            </div>
        </div>
    </div>
</x-app-layout>