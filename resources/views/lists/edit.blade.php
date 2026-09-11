<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit list — JARAK</title>
</head>
<body>

    <h1>Edit list</h1>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('lists.update', $list) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ old('name', $list->name) }}" required>
        <button type="submit">Save</button>
    </form>

    <p><a href="{{ route('lists.index') }}">Back to my lists</a></p>

</body>
</html>