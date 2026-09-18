<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        // List milik sendiri.
        // F5: where() memakai binding PDO otomatis -> prepared statement.
        $myLists = TaskList::where('user_id', $userId)
            ->with(['tasks', 'owner'])
            ->latest('id')
            ->get();

        // List yang dibagikan ke user ini.
        // F1: owner kini juga ada di pivot, jadi list milik sendiri
        // harus dikecualikan agar tidak muncul dobel di dashboard.
        $sharedLists = TaskList::where('user_id', '!=', $userId)
            ->whereHas('collaborators', function ($query) use ($userId) {
                $query->where('list_user.user_id', $userId);
            })
            ->with(['tasks', 'owner'])
            ->latest('id')
            ->get();

        return view('progress.dashboard', compact('myLists', 'sharedLists'));
    }
}
