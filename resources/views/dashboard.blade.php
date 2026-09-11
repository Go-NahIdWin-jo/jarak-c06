<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — JARAK</title>
</head>
<body>

    <h1>Dashboard</h1>
    <p>Logged in as {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <p><a href="{{ route('lists.index') }}">My lists</a></p>

    @if (auth()->user()->role === 'admin')
        <p><a href="{{ route('admin.users.index') }}">Manage users</a></p>
    @endif

</body>
</html>