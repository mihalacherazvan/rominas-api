<?php

declare(strict_types=1);

use Laravel\Sanctum\Sanctum;
use Rominas\Catalog\Artist\Model\Artist;
use Rominas\Users\Model\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

it('lets a super_admin create an artist and auto-generates a slug', function (): void {
    actingAsSuperAdmin();

    postJson('/api/admin/artists', [
        'name' => 'The Motans',
        'description' => 'Romanian act',
    ])->assertStatus(201)
        ->assertJsonPath('data.name', 'The Motans')
        ->assertJsonPath('data.slug', 'the-motans');

    expect(Artist::query()->where('slug', 'the-motans')->exists())->toBeTrue();
});

it('lists, shows, updates and deletes an artist', function (): void {
    actingAsSuperAdmin();
    $artist = Artist::factory()->create();

    getJson('/api/admin/artists')->assertStatus(200);
    getJson("/api/admin/artists/{$artist->id}")->assertStatus(200)->assertJsonPath('data.id', $artist->id);

    patchJson("/api/admin/artists/{$artist->id}", ['name' => 'Renamed'])
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Renamed');

    deleteJson("/api/admin/artists/{$artist->id}")->assertStatus(200);
    expect(Artist::query()->whereKey($artist->id)->exists())->toBeFalse();
});

it('forbids catalog management without permission', function (): void {
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/admin/artists', ['name' => 'X'])->assertStatus(403);
});

it('requires authentication for catalog', function (): void {
    getJson('/api/admin/artists')->assertStatus(401);
});

it('exposes create endpoints for every catalog entity', function (string $path): void {
    actingAsSuperAdmin();

    postJson("/api/admin/{$path}", ['name' => ucfirst($path) . ' One'])
        ->assertStatus(201);
})->with(['artists', 'bands', 'venues', 'songs', 'albums']);
