<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit List
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        <ul class="mb-0 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('lists.update', $list) }}" method="POST" class="flex gap-2">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" class="border-gray-300 rounded flex-1" value="{{ old('name', $list->name) }}" required>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </form>

                <p class="mt-4"><a href="{{ route('lists.index') }}" class="text-blue-600 hover:underline">Back to my lists</a></p>

            </div>
        </div>
    </div>
</x-app-layout>