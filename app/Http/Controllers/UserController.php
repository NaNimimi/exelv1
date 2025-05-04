<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request)
    {
        // if (!Auth::user()->hasPermissionTo('view-users')) {
        //     abort(403, 'Unauthorized action.');
        // }

        $search = $request->input('search');
        $users = User::with('roles') // Eager-load roles
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(15);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => ['search' => $search],
            'title' => 'Foydalanuvchilar',
            'auth' => Auth::user(),
        ]);
    }

    public function create(): Response
    {
        if (!Auth::user()->hasPermissionTo('create-users')) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('create-users')) {
            abort(403, 'Unauthorized action.');
        }

        $this->userService->create($request->validated());

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully.');
    }

    public function show(int $id): Response
    {
        if (!Auth::user()->hasPermissionTo('view-users')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $this->userService->find($id);

        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    public function edit(int $id): Response
    {
        if (!Auth::user()->hasPermissionTo('edit-users')) {
            abort(403, 'Unauthorized action.');
        }

        $user = $this->userService->find($id);

        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('edit-users')) {
            abort(403, 'Unauthorized action.');
        }

        $this->userService->update($id, $request->validated());

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if (!Auth::user()->hasPermissionTo('delete-users')) {
            abort(403, 'Unauthorized action.');
        }

        $this->userService->delete($id);

        return redirect()->route('users.index')
                         ->with('success', 'User deleted successfully.');
    }

    public function getByEmail(string $email): JsonResponse
    {
        $user = $this->userService->findByEmail($email);

        return response()->json([
            'data' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ]
        ]);
    }


}