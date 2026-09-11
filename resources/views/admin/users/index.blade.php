<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Users
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                @endif

                <h3 class="text-lg font-semibold mb-2">Add new user</h3>
                <form action="{{ route('admin.users.store') }}" method="POST" class="mb-8 space-y-3 max-w-sm">
                    @csrf

                    <div>
                        <label class="block text-sm text-gray-600">Name</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded" value="{{ old('name') }}" required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Email</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded" value="{{ old('email') }}" required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Password</label>
                        <input type="password" name="password" class="w-full border-gray-300 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600">Role</label>
                        <select name="role" class="w-full border-gray-300 rounded" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create user</button>
                </form>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                        <ul class="mb-0 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="text-lg font-semibold mb-2">All users</h3>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Name</th>
                            <th class="text-left py-2">Email</th>
                            <th class="text-left py-2">Role</th>
                            <th class="text-right py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b">
                                <td class="py-2">{{ $user->name }}</td>
                                <td class="py-2">{{ $user->email }}</td>
                                <td class="py-2">{{ $user->role }}</td>
                                <td class="py-2 text-right">
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user?')">
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