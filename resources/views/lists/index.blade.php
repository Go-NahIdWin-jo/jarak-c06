<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My lists — JARAK</title>
</head>
<body>

    <h1>My lists</h1>
    <p><a href="{{ route('dashboard') }}">Back to dashboard</a></p>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <h2>Create a new list</h2>
    <form action="{{ route('lists.store') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="List name" value="{{ old('name') }}" required>
        <button type="submit">Create</button>
    </form>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Lists you own</h2>
    @forelse ($ownedLists as $list)
        <div>
            <strong>{{ $list->name }}</strong>
            <a href="{{ route('lists.edit', $list) }}">Edit</a>
            <form action="{{ route('lists.destroy', $list) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this list?')">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </div>
    @empty
        <p>You don't own any lists yet.</p>
    @endforelse

    <h2>Lists you've joined</h2>
    @forelse ($joinedLists as $list)
        <div>
            <strong>{{ $list->name }}</strong> (owned by {{ $list->owner->name }})
        </div>
    @empty
        <p>You haven't joined any lists yet.</p>
    @endforelse

</body>
</html>