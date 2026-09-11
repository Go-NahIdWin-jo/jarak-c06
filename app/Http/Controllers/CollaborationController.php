<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaborationController extends Controller
{
    public function index($listId)
    {
        $list = TaskList::with('collaborators')->findOrFail($listId);
        
        // Cek apakah user yang login punya akses (owner atau collaborator)
        $isOwner = $list->user_id === Auth::id();
        $isCollaborator = $list->collaborators->contains(Auth::id());
        
        if (!$isOwner && !$isCollaborator) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil semua user selain owner dan yang sudah join untuk dropdown invite
        $availableUsers = User::where('id', '!=', $list->user_id)
            ->whereNotIn('id', $list->collaborators->pluck('id'))
            ->get();

        return view('collaborators.index', compact('list', 'availableUsers', 'isOwner'));
    }

    public function invite(Request $request, $listId)
    {
        $list = TaskList::findOrFail($listId);
        
        // Hanya owner yang boleh invite
        if ($list->user_id !== Auth::id()) {
            abort(403, 'Hanya pemilik list yang dapat mengundang kolaborator.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,member'
        ]);

        $list->collaborators()->attach($request->user_id, ['role' => $request->role]);

        return redirect()->back()->with('success', 'Kolaborator berhasil ditambahkan.');
    }

    public function remove($listId, $userId)
    {
        $list = TaskList::findOrFail($listId);
        
        // Hanya owner yang boleh remove
        if ($list->user_id !== Auth::id()) {
            abort(403, 'Hanya pemilik list yang dapat menghapus kolaborator.');
        }

        // Tidak bisa hapus diri sendiri (karena dia ownernya)
        if ($list->user_id == $userId) {
            return redirect()->back()->with('error', 'Pemilik list tidak dapat dihapus.');
        }

        $list->collaborators()->detach($userId);

        return redirect()->back()->with('success', 'Kolaborator berhasil dihapus.');
    }
}
