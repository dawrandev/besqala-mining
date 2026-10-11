<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table): void {
            $table->id();
            // Act number, e.g. "D-2026-0005".
            $table->string('act_number')->unique();
            $table->foreignId('resident_id')->constrained('residents')->cascadeOnDelete();
            // Authority officer who conducted the inspection.
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            // What was inspected, e.g. electricity metering compliance.
            $table->string('subject');
            // planned | in_progress | completed
            $table->string('status')->default('planned');
            // clean | violation
            $table->string('result')->nullable();
            $table->boolean('has_violation')->default(false);
            $table->text('findings')->nullable();
            $table->timestamp('conducted_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
