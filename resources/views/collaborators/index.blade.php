@extends('layouts.app')

@section('content')
    <a href="{{ route('dashboard') }}" class="btn" style="background: #e0e0e0; margin-bottom: 1rem;">&larr; Back to Dashboard</a>

    <h2>Collaborators: {{ $list->name }}</h2>
    <p>Owner: {{ $list->owner->name }}</p>

    <div class="card">
        <h3>Members</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $list->owner->name }} (You)</td>
                    <td>{{ $list->owner->email }}</td>
                    <td>Owner</td>
                    <td>-</td>
                </tr>
                @foreach($list->collaborators as $collab)
                    <tr>
                        <td>{{ $collab->name }}</td>
                        <td>{{ $collab->email }}</td>
                        <td>{{ ucfirst($collab->pivot->role) }}</td>
                        <td>
                            @if($isOwner)
                                <form action="{{ route('collaborators.remove', [$list->id, $collab->id]) }}" method="POST" onsubmit="return confirm('Remove user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($isOwner)
        <div class="card" style="margin-top: 2rem;">
            <h3>Invite Collaborator</h3>
            <form action="{{ route('collaborators.invite', $list->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label>Select User:</label><br>
                    <select name="user_id" required style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;">
                        <option value="">-- Choose User --</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label>Role:</label><br>
                    <select name="role" required style="width: 100%; padding: 0.5rem; margin-top: 0.5rem;">
                        <option value="member">Member</option>
                        <option value="owner">Owner (Co-owner)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Invite User</button>
            </form>
        </div>
    @endif
@endsection
