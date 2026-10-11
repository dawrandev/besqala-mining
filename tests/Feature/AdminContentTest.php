<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Livewire\AboutEditor;
use App\Livewire\LeadershipManager;
use App\Livewire\NewsManager;
use App\Models\Leader;
use App\Models\NewsArticle;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(DemoSeeder::class));

function admin(): User
{
    return User::query()->where('role', UserRole::Admin)->firstOrFail();
}

it('creates a news article via the manager', function (): void {
    Livewire::actingAs(admin())->test(NewsManager::class)
        ->call('create')
        ->set('title.uz', 'Test yangilik')
        ->set('body.uz', 'Matn')
        ->set('published', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('news_articles', []);
    expect(NewsArticle::query()->where('title->uz', 'Test yangilik')->exists())->toBeTrue();
});

it('validates the required news title', function (): void {
    Livewire::actingAs(admin())->test(NewsManager::class)
        ->call('create')
        ->call('save')
        ->assertHasErrors(['title.uz', 'body.uz']);
});

it('adds a leadership member', function (): void {
    $before = Leader::query()->count();

    Livewire::actingAs(admin())->test(LeadershipManager::class)
        ->call('create')
        ->set('full_name.uz', 'Test Rahbar')
        ->set('position.uz', 'Direktor')
        ->call('save')
        ->assertHasNoErrors();

    expect(Leader::query()->count())->toBe($before + 1);
});

it('updates the about page content', function (): void {
    Livewire::actingAs(admin())->test(AboutEditor::class)
        ->set('title.uz', 'Yangilangan sarlavha')
        ->set('intro.uz', 'Yangilangan matn')
        ->call('save')
        ->assertHasNoErrors();

    expect(SiteSetting::get('about')['title']['uz'])->toBe('Yangilangan sarlavha');
});
