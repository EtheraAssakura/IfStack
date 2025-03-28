<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $totalUsers = User::count();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'totalUsers' => $totalUsers
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $sites = Site::all();
        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'sites' => $sites,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'role_id' => 'required|exists:roles,id',
            'site_id' => 'nullable|exists:sites,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'site_id' => $validated['site_id'],
        ]);

        $user->roles()->attach($validated['role_id']);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $sites = Site::all();
        return Inertia::render('Users/Edit', [
            'user' => $user->load(['roles', 'site']),
            'roles' => $roles,
            'sites' => $sites,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'site_id' => 'nullable|exists:sites,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'site_id' => $validated['site_id'],
        ]);

        if (isset($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->sync([$validated['role_id']]);

        return redirect()->back()
            ->with('success', 'Rôle mis à jour avec succès.');
    }
}
