<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    // check that the current user owns the list or is a member before letting them touch it
    private function authorizeListAccess(TaskList $list): void
    {
        $userId = auth()->id();
        $isOwner = $list->user_id === $userId;
        $isMember = $list->members()->where('user_id', $userId)->exists();

        if (! $isOwner && ! $isMember) {
            abort(403);
        }
    }

    // list the tasks in a list, sorted by priority then deadline
    public function index(TaskList $list)
    {
        $this->authorizeListAccess($list);

        $tasks = $list->tasks()
            ->orderByRaw("CASE priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
                ELSE 4
            END")
            ->orderBy('deadline')
            ->get();

        return view('tasks.index', compact('list', 'tasks'));
    }

    // add a task to a list by validating the input and saving it inside a transaction
    public function store(Request $request, TaskList $list)
    {
        $this->authorizeListAccess($list);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        DB::transaction(function () use ($list, $validated) {
            $list->tasks()->create($validated);
        });

        return back()->with('success', 'Task added.');
    }

    // update a task's title, deadline, and priority
    public function update(Request $request, Task $task)
    {
        $this->authorizeListAccess($task->list);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        DB::transaction(function () use ($task, $validated) {
            $task->update($validated);
        });

        return back()->with('success', 'Task updated.');
    }

    // flip a task between done and not done
    public function toggle(Task $task)
    {
        $this->authorizeListAccess($task->list);

        DB::transaction(function () use ($task) {
            $task->update(['is_completed' => ! $task->is_completed]);
        });

        return back();
    }

    // delete a single task
    public function destroy(Task $task)
    {
        $this->authorizeListAccess($task->list);

        DB::transaction(function () use ($task) {
            $task->delete();
        });

        return back()->with('success', 'Task deleted.');
    }
}