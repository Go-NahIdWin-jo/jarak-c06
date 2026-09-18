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

        // F4 — hanya owner yang boleh mengundang
        $this->authorizeOwner($list, 'Hanya pemilik list yang dapat mengundang kolaborator.');

        // F3/F5 — validasi sekaligus cegah duplikat sebelum menyentuh DB
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([$list->user_id]),
                Rule::unique('list_user', 'user_id')->where(function ($query) use ($list) {
                    return $query->where('list_id', $list->id);
                }),
            ],
            'role' => ['required', Rule::in(['viewer', 'member', 'owner'])],
        ], [
            'user_id.not_in'  => 'Pemilik list otomatis menjadi kolaborator dan tidak perlu diundang.',
            'user_id.unique'  => 'User tersebut sudah menjadi kolaborator pada list ini.',
            'role.in'         => 'Role tidak valid.',
        ]);

        // F3 — Database Transaction
        DB::transaction(function () use ($list, $validated) {
            $list->collaborators()->syncWithoutDetaching([
                $validated['user_id'] => ['role' => $validated['role']],
            ]);
        });

        return redirect()
            ->route('collaborators.index', $list->id)
            ->with('success', 'Kolaborator berhasil ditambahkan.');
    }

    public function remove($listId, $userId)
    {
        $list = TaskList::findOrFail($listId);

        // F4 — hanya owner yang boleh menghapus kolaborator
        $this->authorizeOwner($list, 'Hanya pemilik list yang dapat menghapus kolaborator.');

        // Owner tidak boleh dilepas dari listnya sendiri
        if ((int) $list->user_id === (int) $userId) {
            return redirect()->back()->with('error', 'Pemilik list tidak dapat dihapus.');
        }

        // F3 — Database Transaction
        $detached = DB::transaction(function () use ($list, $userId) {
            return $list->collaborators()->detach($userId);
        });

        if ($detached === 0) {
            return redirect()->back()->with('error', 'User tersebut bukan kolaborator pada list ini.');
        }

        return redirect()->back()->with('success', 'Kolaborator berhasil dihapus.');
    }
