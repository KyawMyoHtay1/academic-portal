<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\Users\StoreUserRequest;
use App\Http\Requests\Staff\Users\UpdateUserRequest;
use App\Models\User;
use App\Notifications\ManagementActivityNotification;
use App\Services\ImageService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StaffUserController extends Controller
{
    /**
     * Display a listing of users for staff administration.
     */
    public function index(): Response
    {
        $filters = request()->only(['search', 'role', 'sort_by', 'sort_dir']);
        $search = trim((string) ($filters['search'] ?? ''));
        $role = trim((string) ($filters['role'] ?? 'all'));

        $allowedSorts = ['name', 'email', 'role', 'created_at'];
        $requestedSortBy = (string) ($filters['sort_by'] ?? 'name');
        $sortBy = in_array($requestedSortBy, $allowedSorts, true)
            ? $requestedSortBy
            : 'name';
        $sortDir = ($filters['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($role !== '' && $role !== 'all') {
            $query->where('role', $role);
        }

        $users = $query
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'photo' => $user->photo,
                    'created_at' => $user->created_at,
                ];
            });

        $stats = [
            'total' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'staff' => User::where('role', 'staff')->count(),
        ];

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
            'stats' => $stats,
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
            'duplicateHintUsers' => User::query()
                ->select('id', 'name', 'email')
                ->orderByDesc('id')
                ->limit(500)
                ->get(),
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
                'dob' => $user->student?->dob?->toDateString(),
                'photo_url' => $user->photo
                    ? asset('storage/'.$user->photo)
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
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Generate a random temporary password; user sets a real one via reset link.
        $password = Str::password(32);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($password),
        ];

        if ($request->hasFile('photo')) {
            $userData['photo'] = ImageService::store($request->file('photo'), 'users');
        }

        $user = User::create($userData);

        // Trigger email verification flow for newly created user.
        event(new Registered($user));

        $message = 'User created successfully. Verification and password setup links have been sent.';

        try {
            $status = Password::broker()->sendResetLink(['email' => $user->email]);
            if ($status !== Password::RESET_LINK_SENT) {
                $message = 'User created successfully. Could not send password setup link automatically.';
            }
        } catch (\Throwable $e) {
            Log::warning('staff_user_reset_link_failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            $message = 'User created successfully. Could not send password setup link automatically.';
        }

        $this->notifyUserTargetedChange(
            $user,
            'Account created',
            "Your portal account was created with role {$user->role}."
        );

        $this->notifyUserManagement(
            'User created',
            sprintf(
                '%s created %s (%s) with role %s.',
                $this->actorName(),
                $user->name,
                $user->email,
                $user->role
            ),
            route('admin.users.edit', $user),
            [
                'user_id' => $user->id,
                'action' => 'created',
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $previousRole = (string) $user->role;

        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            ImageService::delete($user->photo);

            $data['photo'] = ImageService::store($request->file('photo'), 'users');
        }

        $user->update($data);
        $user->refresh();

        $roleChanged = $previousRole !== (string) $user->role;
        $targetMessage = $roleChanged
            ? "Your account details were updated. Role changed from {$previousRole} to {$user->role}."
            : 'Your account details were updated by administration.';

        $this->notifyUserTargetedChange($user, 'Account updated', $targetMessage);

        $this->notifyUserManagement(
            'User updated',
            $roleChanged
                ? sprintf(
                    '%s updated %s (%s) and changed role from %s to %s.',
                    $this->actorName(),
                    $user->name,
                    $user->email,
                    $previousRole,
                    $user->role
                )
                : sprintf(
                    '%s updated %s (%s).',
                    $this->actorName(),
                    $user->name,
                    $user->email
                ),
            route('admin.users.edit', $user),
            [
                'user_id' => $user->id,
                'action' => $roleChanged ? 'role_changed' : 'updated',
                'from_role' => $roleChanged ? $previousRole : null,
                'to_role' => $roleChanged ? $user->role : null,
            ]
        );

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
        // Prevent staff from deleting their own account
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account while logged in as this user.');
        }

        $deletedUserId = $user->id;
        $deletedUserName = $user->name;
        $deletedUserEmail = $user->email;

        // Delete stored profile photo, if any
        ImageService::delete($user->photo);

        $user->delete();

        $this->notifyUserManagement(
            'User deleted',
            sprintf(
                '%s deleted %s (%s).',
                $this->actorName(),
                $deletedUserName,
                $deletedUserEmail
            ),
            route('admin.users.index'),
            [
                'user_id' => $deletedUserId,
                'action' => 'deleted',
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = collect($data['ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()
                ->route('admin.users.index')
                ->with('info', 'No users selected for deletion.');
        }

        if (auth()->id() && $ids->contains((int) auth()->id())) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account in bulk actions.');
        }

        $users = User::query()
            ->whereIn('id', $ids->all())
            ->get(['id', 'name', 'email', 'photo']);

        foreach ($users as $user) {
            ImageService::delete($user->photo);
        }

        User::query()->whereIn('id', $ids->all())->delete();

        $deletedCount = $users->count();
        $sampleLabels = $users
            ->take(3)
            ->map(fn (User $user) => "{$user->name} ({$user->email})")
            ->implode(', ');
        $sampleSuffix = $sampleLabels !== '' ? " Example: {$sampleLabels}." : '';

        $this->notifyUserManagement(
            'Users deleted',
            sprintf(
                '%s deleted %d user account(s).%s',
                $this->actorName(),
                $deletedCount,
                $sampleSuffix
            ),
            route('admin.users.index'),
            [
                'action' => 'bulk_deleted',
                'count' => $deletedCount,
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$users->count()} user(s) deleted successfully.");
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function notifyUserManagement(
        string $title,
        string $message,
        ?string $url = null,
        array $meta = []
    ): void {
        $recipients = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->get(['id', 'role', 'preferences']);

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send(
                $recipients,
                new ManagementActivityNotification('users', $title, $message, $url, $meta)
            );
        } catch (\Throwable $e) {
            Log::warning('staff_user_management_notification_failed', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyUserTargetedChange(User $recipient, string $title, string $message): void
    {
        if (in_array((string) $recipient->role, ['staff', 'admin'], true)) {
            return;
        }

        try {
            $recipient->notify(new ManagementActivityNotification(
                'users',
                $title,
                $message,
                route('settings.index'),
                [
                    'user_id' => $recipient->id,
                    'action' => 'targeted_update',
                ]
            ));
        } catch (\Throwable $e) {
            Log::warning('staff_user_target_notification_failed', [
                'user_id' => $recipient->id,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function actorName(): string
    {
        return Auth::user()?->name ?? 'System';
    }
}
