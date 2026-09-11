<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    public function index()
    {
        $ownedLists = auth()->user()->lists;
        $joinedLists = auth()->user()->joinedLists;

        return view('lists.index', compact('ownedLists', 'joinedLists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        TaskList::create([
            'name' => $validated['name'],
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'List created.');
    }

    public function edit(TaskList $list)
    {
        if ($list->user_id !== auth()->id()) {
            abort(403);
        }

        return view('lists.edit', compact('list'));
    }

    public function update(Request $request, TaskList $list)
    {
        if ($list->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $list->update($validated);

        return redirect()->route('lists.index')->with('success', 'List updated.');
    }

    public function destroy(TaskList $list)
    {
        if ($list->user_id !== auth()->id()) {
            abort(403);
        }

        $list->delete();

        return back()->with('success', 'List deleted.');
    }
}