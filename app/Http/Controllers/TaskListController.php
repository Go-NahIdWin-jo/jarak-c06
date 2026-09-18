<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskListController extends Controller
{
    // show the lists the user owns plus the lists they've joined as a collaborator
    public function index()
    {
        $ownedLists = auth()->user()->lists;
        $joinedLists = auth()->user()->joinedLists;

        return view('lists.index', compact('ownedLists', 'joinedLists'));
    }

    // create a list and auto-attach the creator as its owner in list_user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $list = TaskList::create([
                'name' => $validated['name'],
                'user_id' => auth()->id(),
            ]);

            $list->collaborators()->attach(auth()->id(), ['role' => 'owner']);
        });

        return back()->with('success', 'List created.');
    }

    // show the edit form, only if the current user owns the list
    public function edit(TaskList $list)
    {
        if ($list->user_id !== auth()->id()) {
            abort(403);
        }

        return view('lists.edit', compact('list'));
    }

    // rename a list, only if the current user owns it
    public function update(Request $request, TaskList $list)
    {
        if ($list->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($list, $validated) {
            $list->update($validated);
        });

        return redirect()->route('lists.index')->with('success', 'List updated.');
    }

    // delete a list and cascade-remove its tasks and collaborator memberships
    public function destroy(TaskList $list)
    {
        if ($list->user_id !== auth()->id()) {
            abort(403);
        }

        DB::transaction(function () use ($list) {
            $list->tasks()->delete();
            $list->collaborators()->detach();
            $list->delete();
        });

        return back()->with('success', 'List deleted.');
    }
}