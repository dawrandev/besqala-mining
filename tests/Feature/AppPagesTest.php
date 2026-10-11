<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DemoSeeder;

function demoUser(UserRole $role): User
{
    return User::query()->where('role', $role)->firstOrFail();
}

beforeEach(function (): void {
    $this->seed(DemoSeeder::class);
});

it('shows each role its dashboard', function (UserRole $role): void {
    $this->actingAs(demoUser($role))->get('/app')->assertOk();
})->with([
    'applicant' => [UserRole::Applicant],
    'direksiya' => [UserRole::Direksiya],
    'expert' => [UserRole::Expert],
    'authority' => [UserRole::Authority],
    'admin' => [UserRole::Admin],
]);

it('serves applicant pages', function (string $uri): void {
    $this->actingAs(demoUser(UserRole::Applicant))->get($uri)->assertOk();
})->with(['/app/applications', '/app/new', '/app/cabinet']);

it('serves direksiya pages', function (string $uri): void {
    $this->actingAs(demoUser(UserRole::Direksiya))->get($uri)->assertOk();
})->with(['/app/queue', '/app/residents', '/app/reports']);

it('serves expert, authority and admin pages', function (UserRole $role, string $uri): void {
    $this->actingAs(demoUser($role))->get($uri)->assertOk();
})->with([
    [UserRole::Expert, '/app/review'],
    [UserRole::Authority, '/app/permits'],
    [UserRole::Authority, '/app/oversight'],
    [UserRole::Admin, '/app/admin/news'],
    [UserRole::Admin, '/app/admin/leadership'],
    [UserRole::Admin, '/app/admin/about'],
]);

it('forbids a role from another role’s pages', function (): void {
    $this->actingAs(demoUser(UserRole::Applicant))->get('/app/queue')->assertForbidden();
    $this->actingAs(demoUser(UserRole::Expert))->get('/app/permits')->assertForbidden();
});

it('lets an applicant open their own application detail', function (): void {
    $applicant = demoUser(UserRole::Applicant);
    $application = $applicant->applications()->firstOrFail();
    $this->actingAs($applicant)->get("/app/a/{$application->id}")->assertOk();
});
