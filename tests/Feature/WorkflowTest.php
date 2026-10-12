<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\ResidentStatus;
use App\Enums\UserRole;
use App\Models\Application;
use App\Models\User;
use Database\Seeders\DemoSeeder;

beforeEach(fn () => $this->seed(DemoSeeder::class));

function userWithRole(UserRole $role): User
{
    return User::query()->where('role', $role)->firstOrFail();
}

it('lets an applicant submit a new application', function (): void {
    $applicant = userWithRole(UserRole::Applicant);

    $this->actingAs($applicant)
        ->postJson(route('app.applications.store'), [
            'company_name' => '«Test Mining» MChJ',
            'stir' => '301234567',
            'region' => 'nukus_city',
            'address' => 'Nukus, Test 1',
            'submit' => true,
        ])
        ->assertOk()
        ->assertJson(['ok' => true]);

    $this->assertDatabaseHas('applications', [
        'company_name' => '«Test Mining» MChJ',
        'status' => ApplicationStatus::Submitted->value,
    ]);
});

it('validates required fields on submit', function (): void {
    $this->actingAs(userWithRole(UserRole::Applicant))
        ->postJson(route('app.applications.store'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['company_name', 'stir', 'region', 'address']);
});

it('runs an application through the full pipeline to registration', function (): void {
    $application = Application::query()->create([
        'number' => 'A-2026-9001',
        'applicant_id' => userWithRole(UserRole::Applicant)->id,
        'type' => App\Enums\ApplicationType::Registration,
        'status' => ApplicationStatus::Submitted,
        'company_name' => '«Pipeline» MChJ',
        'stir' => '309999999',
        'region' => 'beruniy',
        'legal_form' => 'MChJ',
        'address' => 'Beruniy, 1',
        'submitted_at' => now(),
    ]);

    // Direksiya -> expert review
    $this->actingAs(userWithRole(UserRole::Direksiya))
        ->postJson(route('app.applications.action', $application), ['action' => 'expert'])
        ->assertOk();
    expect($application->refresh()->status)->toBe(ApplicationStatus::ExpertReview);

    // Expert -> positive -> authority review
    $this->actingAs(userWithRole(UserRole::Expert))
        ->postJson(route('app.applications.action', $application), ['action' => 'conclude', 'verdict' => 'positive'])
        ->assertOk();
    expect($application->refresh()->status)->toBe(ApplicationStatus::AuthorityReview);

    // Authority -> issue permit -> registered
    $this->actingAs(userWithRole(UserRole::Authority))
        ->postJson(route('app.applications.action', $application), ['action' => 'permit'])
        ->assertOk();

    $application->refresh();
    expect($application->status)->toBe(ApplicationStatus::Registered)
        ->and($application->resident_id)->not->toBeNull();

    $this->assertDatabaseHas('documents', ['application_id' => $application->id, 'kind' => DocumentKind::Certificate->value]);
    $this->assertDatabaseHas('documents', ['application_id' => $application->id, 'kind' => DocumentKind::Permit->value]);
    $this->assertDatabaseHas('residents', ['id' => $application->resident_id, 'status' => ResidentStatus::Active->value]);
});

it('forbids the wrong role from acting', function (): void {
    $application = Application::query()->where('status', ApplicationStatus::CompletenessCheck)->firstOrFail();

    $this->actingAs(userWithRole(UserRole::Applicant))
        ->postJson(route('app.applications.action', $application), ['action' => 'expert'])
        ->assertForbidden();
});
