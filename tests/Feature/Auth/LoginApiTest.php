<?php

use App\Domain\Auth\Models\LoginHistory;
use App\Models\User;

it('logs in an active seeded user and returns a JWT token', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'masyarakat@gcms.local',
        'password' => 'password',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'user' => [
                'id',
                'name',
                'email',
                'roles',
            ],
        ])
        ->assertJsonPath('token_type', 'bearer')
        ->assertJsonPath('user.email', 'masyarakat@gcms.local');

    $user = User::query()->where('email', 'masyarakat@gcms.local')->firstOrFail();

    expect($user->refresh()->last_login_at)->not->toBeNull()
        ->and(LoginHistory::query()->where('email', $user->email)->where('status', 'success')->exists())->toBeTrue();
});

it('rejects invalid credentials and records a failed login history', function (): void {
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'masyarakat@gcms.local',
        'password' => 'wrong-password',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);

    expect(LoginHistory::query()
        ->where('email', 'masyarakat@gcms.local')
        ->where('status', 'failed')
        ->where('failure_reason', 'Invalid credentials.')
        ->exists())->toBeTrue();
});
