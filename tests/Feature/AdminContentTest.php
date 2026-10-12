<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Leader;
use App\Models\NewsArticle;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DemoSeeder;

beforeEach(fn () => $this->seed(DemoSeeder::class));

function admin(): User
{
    return User::query()->where('role', UserRole::Admin)->firstOrFail();
}

it('creates a news article via the endpoint', function (): void {
    $this->actingAs(admin())
        ->postJson(route('app.admin.news.store'), [
            'title' => ['uz' => 'Test yangilik'],
            'body' => ['uz' => 'Matn'],
            'category' => 'official',
            'published' => true,
        ])
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect(NewsArticle::query()->where('title->uz', 'Test yangilik')->exists())->toBeTrue();
});

it('validates the required news title', function (): void {
    $this->actingAs(admin())
        ->postJson(route('app.admin.news.store'), [
            'title' => ['uz' => ''],
            'body' => ['uz' => ''],
            'category' => 'official',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title.uz', 'body.uz']);
});

it('adds a leadership member', function (): void {
    $before = Leader::query()->count();

    $this->actingAs(admin())
        ->postJson(route('app.admin.leadership.store'), [
            'full_name' => ['uz' => 'Test Rahbar'],
            'position' => ['uz' => 'Direktor'],
        ])
        ->assertOk();

    expect(Leader::query()->count())->toBe($before + 1);
});

it('updates the about page content', function (): void {
    $this->actingAs(admin())
        ->putJson(route('app.admin.about.update'), [
            'title' => ['uz' => 'Yangilangan sarlavha'],
            'intro' => ['uz' => 'Yangilangan matn'],
        ])
        ->assertOk();

    expect(SiteSetting::get('about')['title']['uz'])->toBe('Yangilangan sarlavha');
});
