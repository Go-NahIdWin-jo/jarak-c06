<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;



class CollaborationController extends Controller
{
    // guard level owner
    private function authorizeOwner(Tasklist $list, string $message): void
    {
	if($list->user_id !== Auth:id()){
	    abort(403,$message);
	}
    }

    // guard level member
    private function authorizeMember(Tasklist $list, string $message): void
    {
	$isOwner = $list->user_id === Auth::id();
	$isCollaborator = $list->collaborators->contains('id',Auth::id());

	if(! $isOwner && ! $isCollaborator){
	    abort(403,'Unauthorized action.');
	}
    }

    public function index($listId)
    {
        $list = TaskList::with('collaborators')->findOrFail($listId);

        $this->authorizeMember($list);

        $isOwner = $list->user_id === Auth::id();

        // F1: owner kini ikut tercatat di pivot list_user,
        // jadi kita keluarkan dari daftar members agar tidak tampil dobel.
        $members = $list->collaborators->where('id', '!=', $list->user_id);

        // Kandidat undangan: semua user kecuali owner & yang sudah jadi kolaborator
        $availableUsers = User::where('id', '!=', $list->user_id)
            ->whereNotIn('id', $list->collaborators->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('collaborators.index', compact('list', 'members', 'availableUsers', 'isOwner'));
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
