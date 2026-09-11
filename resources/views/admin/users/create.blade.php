<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah User Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.users.store') }}" method="POST"
                  class="bg-white shadow rounded p-6">
                @csrf

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border rounded p-2" required>
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border rounded p-2" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Password</label>
                    <input type="password" name="password"
                           class="w-full border rounded p-2" required>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 font-medium">Role</label>
                    <select name="role" class="w-full border rounded p-2">
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Simpan
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="px-4 py-2 rounded border">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>