<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private function authorizeListAccess(TaskList $list): void
    {
        $userId = auth()->id();
        $isOwner = $list->user_id === $userId;
        $isMember = $list->members()->where('user_id', $userId)->exists();

        if (! $isOwner && ! $isMember) {
            abort(403);
        }
    }

    public function index(TaskList $list)
    {
        $this->authorizeListAccess($list);

        $tasks = $list->tasks()
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('deadline')
            ->get();

        return view('tasks.index', compact('list', 'tasks'));
    }

    public function store(Request $request, TaskList $list)
    {
        $this->authorizeListAccess($list);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $list->tasks()->create($validated);

        return back()->with('success', 'Task added.');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeListAccess($task->list);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $task->update($validated);

        return back()->with('success', 'Task updated.');
    }

    public function toggle(Task $task)
    {
        $this->authorizeListAccess($task->list);

        $task->update(['is_completed' => ! $task->is_completed]);

        return back();
    }

    public function destroy(Task $task)
    {
        $this->authorizeListAccess($task->list);

        $task->delete();

        return back()->with('success', 'Task deleted.');
    }
}