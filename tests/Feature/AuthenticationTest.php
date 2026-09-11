<?php

declare(strict_types=1);

use Rominas\Users\Model\User;

use function Pest\Laravel\postJson;

it('issues a Sanctum token for valid admin credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'admin@rominas.test',
        // UserFactory hashes 'password' by default.
    ]);

    $response = postJson('/api/authenticate', [
        'email' => 'admin@rominas.test',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['userId', 'token']);

    expect($response->json('userId'))->toBe($user->id);
    expect($user->tokens()->count())->toBe(1);
});

it('issues a token with full abilities — admin authorization is via policies, not token abilities', function (): void {
    $user = User::factory()->create(['email' => 'admin@rominas.test']);

    postJson('/api/authenticate', [
        'email' => 'admin@rominas.test',
        'password' => 'password',
    ])->assertStatus(200);

    expect($user->tokens()->first()->abilities)->toBe(['*']);
});

it('rejects invalid credentials with 401', function (): void {
    User::factory()->create(['email' => 'admin@rominas.test']);

    postJson('/api/authenticate', [
        'email' => 'admin@rominas.test',
        'password' => 'wrong-password',
    ])->assertStatus(401);
});

it('validates the authenticate request', function (): void {
    postJson('/api/authenticate', [])->assertStatus(422);
});
