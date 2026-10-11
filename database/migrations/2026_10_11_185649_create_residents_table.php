<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table): void {
            $table->id();
            // The applicant user who owns this resident company.
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('company_name');
            $table->string('stir', 9)->unique()->comment('Taxpayer ID (STIR)');
            $table->string('region')->comment('One of App\Enums\Region');
            $table->string('legal_form')->default('MChJ');
            $table->string('status')->default('active')->comment('One of App\Enums\ResidentStatus');
            $table->string('address');
            $table->string('location_address')->nullable()->comment('Physical mining site location');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            // Array of {name, share} founders, mirrors the original registry shape.
            $table->json('founders')->nullable();
            // Declared mining capacity in megawatts and energy source.
            $table->decimal('power_mw', 8, 2)->nullable();
            $table->string('energy_source')->nullable();
            // Lifetime mined amount in BTC, shown on the resident dashboard.
            $table->decimal('mined_total_btc', 12, 4)->default(0);
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->index('region');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
