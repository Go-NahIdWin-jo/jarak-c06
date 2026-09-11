<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $listIds = $user->lists->pluck('id')
            ->merge($user->joinedLists->pluck('id'));

        $upcomingTasks = \App\Models\Task::whereIn('list_id', $listIds)
            ->where('is_completed', false)
            ->orderBy('deadline')
            ->with('list')
            ->take(10)
            ->get();

        return view('dashboard', compact('upcomingTasks'));
    }
}