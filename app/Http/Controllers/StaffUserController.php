<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffUserController extends Controller
{
    /**
     * Display a listing of users for staff administration.
     */
    public function index(): Response
    {
        $users = User::orderBy('name')
            ->get([
                'id',
                'name',
                'email',
                'role',
                'created_at',
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for editing a user (including role assignment).
     */
    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'availableRoles' => [
                'student',
                'teacher',
                'staff',
            ],
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:student,teacher,staff'],
        ]);

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }
}

