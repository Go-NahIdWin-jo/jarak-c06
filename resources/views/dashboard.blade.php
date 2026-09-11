@extends('layouts.app')

@section('content')
    <h2>Dashboard - Progress Tracking</h2>

    <h3>My Lists</h3>
    @if($myLists->isEmpty())
        <p>You don't have any lists yet.</p>
    @else
        @foreach($myLists as $list)
            <div class="card">
                <h4>{{ $list->name }} (Owner: You)</h4>
                <p>Tasks: {{ $list->tasks->where('is_completed', true)->count() }} / {{ $list->tasks->count() }} completed</p>
                
                @php $progress = $list->progressPercentage(); @endphp
                <div class="progress-bar-container">
                    <div class="progress-bar" 
                         style="width: {{ $progress }}%; background-color: {{ $progress < 30 ? '#dc3545' : ($progress < 70 ? '#ffc107' : '#28a745') }};">
                        {{ $progress }}%
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <a href="{{ route('collaborators.index', $list->id) }}" class="btn btn-primary">Manage Collaborators</a>
                </div>
            </div>
        @endforeach
    @endif

    <h3 style="margin-top: 2rem;">Shared With Me</h3>
    @if($sharedLists->isEmpty())
        <p>No lists shared with you.</p>
    @else
        @foreach($sharedLists as $list)
            <div class="card">
                <h4>{{ $list->name }} (Owner: {{ $list->owner->name }})</h4>
                <p>Tasks: {{ $list->tasks->where('is_completed', true)->count() }} / {{ $list->tasks->count() }} completed</p>
                
                @php $progress = $list->progressPercentage(); @endphp
                <div class="progress-bar-container">
                    <div class="progress-bar" 
                         style="width: {{ $progress }}%; background-color: {{ $progress < 30 ? '#dc3545' : ($progress < 70 ? '#ffc107' : '#28a745') }};">
                        {{ $progress }}%
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <a href="{{ route('collaborators.index', $list->id) }}" class="btn btn-primary">View Collaborators</a>
                </div>
            </div>
        @endforeach
    @endif
@endsection
