@extends('layouts.app')

@section('title', 'Dashboard — JARAK')

@section('content')

    <h1>Dashboard</h1>
    <p class="text-muted">Logged in as {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>

    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('lists.index') }}" class="btn btn-primary">My lists</a>

        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Manage users</a>
        @endif
    </div>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
    </form>

@endsection