<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        // Ambil list milik sendiri
        $myLists = TaskList::where('user_id', $userId)->with('tasks', 'owner')->get();

        // Ambil list dimana user diundang sebagai kolaborator
        $sharedLists = TaskList::whereHas('collaborators', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('tasks', 'owner')->get();

        return view('dashboard', compact('myLists', 'sharedLists'));
    }
}
