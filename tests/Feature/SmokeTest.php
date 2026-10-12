<?php

declare(strict_types=1);

use Database\Seeders\DemoSeeder;

it('renders the public home page', function (): void {
    $this->seed(DemoSeeder::class);
    $this->get('/')->assertOk()->assertSee('Mining Valley');
});

it('renders the login page with demo accounts', function (): void {
    $this->get('/login')->assertOk()->assertSee('demo1234');
});

it('renders the registration page', function (): void {
    $this->get('/register')->assertOk();
});

it('renders public pages with seeded content', function (string $uri): void {
    $this->seed(DemoSeeder::class);
    $this->get($uri)->assertOk();
})->with([
    'registry' => ['/registry'],
    'verify' => ['/verify'],
    'verify with number' => ['/verify?no=G-2026-0001'],
    'news index' => ['/news'],
    'about' => ['/about'],
    'leadership' => ['/leadership'],
]);

it('shows a single news article', function (): void {
    $this->seed(DemoSeeder::class);
    $slug = App\Models\NewsArticle::query()->value('slug');
    $this->get("/news/{$slug}")->assertOk();
});
