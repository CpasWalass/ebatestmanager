<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::paginate(20);

        return response()->json($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        if (!empty($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $user->assignRole($role->name);
            }
        }

        return response()->json(['message' => 'Utilisateur créé', 'data' => $user], 201);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        if (array_key_exists('role', $data)) {
            $user->syncRoles($data['role'] ? [$data['role']] : []);
        }

        return response()->json(['message' => 'Utilisateur mis à jour', 'data' => $user]);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    /** Assign a user to a test case (wrapper for TestCaseAssignment) */
    public function assign(Request $request, User $user): JsonResponse
    {
        $this->authorize('assign', User::class);

        // Minimal endpoint to attach role or simple metadata; actual assignment happens via TestCaseAssignment endpoints
        $payload = $request->only(['meta']);

        $user->update(['meta' => $payload['meta'] ?? null]);

        return response()->json(['message' => 'Utilisateur mis à jour (assign)', 'data' => $user]);
    }
}
