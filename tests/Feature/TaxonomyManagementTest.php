<?php

declare(strict_types=1);

use Laravel\Sanctum\Sanctum;
use Rominas\Taxonomies\Model\Taxonomy;
use Rominas\Users\Model\User;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('lets a super_admin create a taxonomy', function (): void {
    actingAsSuperAdmin();

    postJson('/api/admin/taxonomies', [
        'name' => 'Genre',
        'hierarchical' => false,
        'allows_multiple' => true,
    ])->assertStatus(201)
        ->assertJsonPath('data.name', 'Genre');

    expect(Taxonomy::query()->where('name', 'Genre')->exists())->toBeTrue();
});

it('lets a super_admin list taxonomies', function (): void {
    actingAsSuperAdmin();
    Taxonomy::factory()->count(3)->create();

    getJson('/api/admin/taxonomies')
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('lets a super_admin delete a taxonomy', function (): void {
    actingAsSuperAdmin();
    $taxonomy = Taxonomy::factory()->create();

    deleteJson('/api/admin/taxonomies/' . $taxonomy->id)
        ->assertStatus(200)
        ->assertJsonPath('success', true);

    expect(Taxonomy::query()->whereKey($taxonomy->id)->exists())->toBeFalse();
});

it('forbids a user without permissions from creating a taxonomy', function (): void {
    Sanctum::actingAs(User::factory()->create());

    postJson('/api/admin/taxonomies', [
        'name' => 'Genre',
        'hierarchical' => false,
        'allows_multiple' => false,
    ])->assertStatus(403);
});

it('forbids a user without permissions from listing taxonomies', function (): void {
    Sanctum::actingAs(User::factory()->create());

    getJson('/api/admin/taxonomies')->assertStatus(403);
});

it('requires authentication', function (): void {
    getJson('/api/admin/taxonomies')->assertStatus(401);
});

it('validates the create taxonomy request', function (): void {
    actingAsSuperAdmin();

    postJson('/api/admin/taxonomies', [])->assertStatus(422);
});
