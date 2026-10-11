<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table): void {
            $table->id();
            // Human-facing number, e.g. "A-2026-0017".
            $table->string('number')->unique();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            // Linked once the company is registered as a resident.
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->string('type')->default('registration')->comment('One of App\Enums\ApplicationType');
            $table->string('status')->default('draft')->comment('One of App\Enums\ApplicationStatus');

            // Snapshot of the company data being applied for.
            $table->string('company_name');
            $table->string('stir', 9)->nullable();
            $table->string('region')->comment('One of App\Enums\Region');
            $table->string('legal_form')->default('MChJ');
            $table->string('address')->nullable();
            $table->string('location_address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('founders')->nullable();
            // Array of {type, model, count, power_kw} mining equipment.
            $table->json('equipment')->nullable();
            $table->decimal('power_mw', 8, 2)->nullable();
            $table->string('energy_source')->nullable();
            $table->string('bank_account')->nullable();
            // Uploaded supporting document references.
            $table->json('attachments')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
