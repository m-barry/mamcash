<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderByDesc('id')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function toggleActive(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['active' => !$user->active]);

        return back()->with('success', 'Statut utilisateur mis à jour.');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->withErrors(['Impossible de supprimer un administrateur.']);
        }

        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}
