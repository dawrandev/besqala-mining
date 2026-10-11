<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Enums\DocumentKind;
use App\Enums\ResidentStatus;
use App\Enums\UserRole;
use App\Livewire\ApplicationActions;
use App\Livewire\ApplicationForm;
use App\Models\Application;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(DemoSeeder::class));

function userWithRole(UserRole $role): User
{
    return User::query()->where('role', $role)->firstOrFail();
}

it('lets an applicant submit a new application', function (): void {
    $applicant = userWithRole(UserRole::Applicant);

    Livewire::actingAs($applicant)->test(ApplicationForm::class)
        ->set('company_name', '«Test Mining» MChJ')
        ->set('stir', '301234567')
        ->set('region', 'nukus_city')
        ->set('address', 'Nukus, Test 1')
        ->call('save', true)
        ->assertHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('applications', [
        'company_name' => '«Test Mining» MChJ',
        'status' => ApplicationStatus::Submitted->value,
    ]);
});

it('validates required fields on submit', function (): void {
    Livewire::actingAs(userWithRole(UserRole::Applicant))->test(ApplicationForm::class)
        ->call('save', true)
        ->assertHasErrors(['company_name', 'stir', 'region', 'address']);
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
    Livewire::actingAs(userWithRole(UserRole::Direksiya))->test(ApplicationActions::class, ['application' => $application])
        ->call('sendToExpert')->assertHasNoErrors();
    expect($application->refresh()->status)->toBe(ApplicationStatus::ExpertReview);

    // Expert -> positive -> authority review
    Livewire::actingAs(userWithRole(UserRole::Expert))->test(ApplicationActions::class, ['application' => $application->refresh()])
        ->call('conclude', 'positive')->assertHasNoErrors();
    expect($application->refresh()->status)->toBe(ApplicationStatus::AuthorityReview);

    // Authority -> issue permit -> registered
    Livewire::actingAs(userWithRole(UserRole::Authority))->test(ApplicationActions::class, ['application' => $application->refresh()])
        ->call('issuePermit')->assertHasNoErrors();

    $application->refresh();
    expect($application->status)->toBe(ApplicationStatus::Registered)
        ->and($application->resident_id)->not->toBeNull();

    $this->assertDatabaseHas('documents', ['application_id' => $application->id, 'kind' => DocumentKind::Certificate->value]);
    $this->assertDatabaseHas('documents', ['application_id' => $application->id, 'kind' => DocumentKind::Permit->value]);
    $this->assertDatabaseHas('residents', ['id' => $application->resident_id, 'status' => ResidentStatus::Active->value]);
});

it('forbids the wrong role from acting', function (): void {
    $application = Application::query()->where('status', ApplicationStatus::CompletenessCheck)->firstOrFail();

    Livewire::actingAs(userWithRole(UserRole::Applicant))->test(ApplicationActions::class, ['application' => $application])
        ->call('sendToExpert')
        ->assertForbidden();
});
