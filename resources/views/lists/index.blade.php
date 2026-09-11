@extends('layouts.app')

@section('title', 'My lists — JARAK')

@section('content')

    <h1>My lists</h1>
    <p><a href="{{ route('dashboard') }}">Back to dashboard</a></p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Create a new list</h2>
    <form action="{{ route('lists.store') }}" method="POST" class="d-flex gap-2 mb-4">
        @csrf
        <input type="text" name="name" class="form-control" placeholder="List name" value="{{ old('name') }}" required>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>

    <h2>Lists you own</h2>
    @if ($ownedLists->isEmpty())
        <p class="text-muted">You don't own any lists yet.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ownedLists as $list)
                    <tr>
                        <td>{{ $list->name }}</td>
                        <td class="text-end">
                            <a href="{{ route('lists.edit', $list) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('lists.destroy', $list) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this list?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Lists you've joined</h2>
    @if ($joinedLists->isEmpty())
        <p class="text-muted">You haven't joined any lists yet.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Owner</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($joinedLists as $list)
                    <tr>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->owner->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endsection