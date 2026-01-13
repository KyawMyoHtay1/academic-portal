<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
                'photo',
                'created_at',
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'availableRoles' => [
                'student',
                'teacher',
                'staff',
            ],
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
                'photo_url' => $user->photo
                    ? asset('storage/' . $user->photo)
                    : null,
            ],
            'availableRoles' => [
                'student',
                'teacher',
                'staff',
            ],
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:student,teacher,staff'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        // Simple initial password for demonstration; in a real system you would trigger a reset.
        $password = 'Password123!';

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($password),
        ];

        if ($request->hasFile('photo')) {
            $userData['photo'] = ImageService::store($request->file('photo'), 'users');
        }

        User::create($userData);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully. An initial password has been set.');
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
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            ImageService::delete($user->photo);

            $data['photo'] = ImageService::store($request->file('photo'), 'users');
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the photo from the specified user.
     */
    public function removePhoto(User $user): RedirectResponse
    {
        // Delete photo file if exists
        ImageService::delete($user->photo);

        // Remove photo reference from database
        $user->update(['photo' => null]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User photo removed successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

