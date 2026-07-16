<?php

namespace App\Application\Auth\Services;

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Auth\Models\LoginHistory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * @return array{token: string, user: User}
     */
    public function login(string $email, string $password, ?string $ip = null, ?string $ua = null): array
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user instanceof User || ! Hash::check($password, $user->password)) {
            $this->recordLoginHistory(null, $email, $ip, $ua, 'failed', 'Invalid credentials.');

            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        if (! $user->is_active) {
            $this->recordLoginHistory($user, $email, $ip, $ua, 'failed', 'User is inactive.');

            throw ValidationException::withMessages([
                'email' => 'This account is inactive.',
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ])->save();

        $this->recordLoginHistory($user, $email, $ip, $ua, 'success');

        return [
            'token' => $token,
            'user' => $user->refresh(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): User
    {
        $role = (string) ($data['role'] ?? UserRole::MASYARAKAT->value);
        unset($data['role']);

        if (isset($data['password'])) {
            $data['password'] = Hash::make((string) $data['password']);
        }

        $user = User::query()->create($data);

        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);
        }

        return $user->refresh();
    }

    public function logout(?string $token = null): void
    {
        if ($token !== null) {
            JWTAuth::setToken($token);
        }

        JWTAuth::parseToken()->invalidate();
    }

    private function recordLoginHistory(
        ?User $user,
        string $email,
        ?string $ip,
        ?string $ua,
        string $status,
        ?string $failureReason = null,
    ): void {
        LoginHistory::query()->create([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'status' => $status,
            'failure_reason' => $failureReason,
        ]);
    }
}
