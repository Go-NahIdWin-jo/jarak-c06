<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Back to Dashboard</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Collaborators: {{ $list->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-lg">{{ session('error') }}</div>
            @endif

            <!-- Members Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-500 text-sm mb-4">Owner: <span class="font-medium text-gray-700">{{ $list->owner->name }}</span></p>
                <h3 class="text-lg font-semibold mb-4">Members</h3>
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="border-b text-gray-500">
                            <th class="text-left py-2">Name</th>
                            <th class="text-left py-2">Email</th>
                            <th class="text-left py-2">Role</th>
                            <th class="text-left py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $list->owner->name }} (You)</td>
                            <td class="py-2">{{ $list->owner->email }}</td>
                            <td class="py-2"><span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Owner</span></td>
                            <td class="py-2">-</td>
                        </tr>
                        @foreach($list->collaborators as $collab)
                            <tr class="border-b">
                                <td class="py-2">{{ $collab->name }}</td>
                                <td class="py-2">{{ $collab->email }}</td>
                                <td class="py-2"><span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">{{ ucfirst($collab->pivot->role) }}</span></td>
                                <td class="py-2">
                                    @if($isOwner)
                                        <form action="{{ route('collaborators.remove', [$list->id, $collab->id]) }}" method="POST" onsubmit="return confirm('Remove user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-xs">Remove</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Invite Form -->
            @if($isOwner)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Invite Collaborator</h3>
                    <form action="{{ route('collaborators.invite', $list->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select User:</label>
                            <select name="user_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                                <option value="">-- Choose User --</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role:</label>
                            <select name="role" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                                <option value="member">Member</option>
                                <option value="owner">Owner (Co-owner)</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                            Invite User
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
