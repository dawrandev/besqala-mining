<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            // The staff or applicant user who caused the event (nullable for system events).
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            // Short machine key, e.g. "submitted", "deficiency_sent", "expert_positive".
            $table->string('type');
            // Localised title + optional free-text note rendered in the timeline.
            $table->string('title');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_events');
    }
};
