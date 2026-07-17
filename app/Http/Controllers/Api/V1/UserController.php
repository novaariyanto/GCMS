<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->with(['opd', 'unit'])
            ->when($request->query('q'), function ($query, string $search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate((int) $request->integer('per_page', 15));

        return UserResource::collection($users);
    }

    public function store(Request $request): UserResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:users,nik'],
            'opd_id' => ['nullable', 'uuid', 'exists:opds,id'],
            'unit_id' => ['nullable', 'uuid', 'exists:units,id'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::query()->create($data);

        return new UserResource($user->load(['opd', 'unit']));
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load(['opd', 'unit']));
    }

    public function update(Request $request, User $user): UserResource
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('users', 'nik')->ignore($user->id)],
            'opd_id' => ['nullable', 'uuid', 'exists:opds,id'],
            'unit_id' => ['nullable', 'uuid', 'exists:units,id'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['sometimes', Password::defaults()],
        ]);

        $user->update($data);

        return new UserResource($user->refresh()->load(['opd', 'unit']));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
