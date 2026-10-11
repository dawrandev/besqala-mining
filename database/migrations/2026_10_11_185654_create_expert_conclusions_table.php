<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expert_conclusions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('expert_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('verdict')->comment('One of App\Enums\ExpertVerdict');
            $table->text('comment')->nullable();
            // Commission members present, e.g. ["R. Nurmuxamedov (rais)", "D. Li"].
            $table->json('members')->nullable();
            $table->timestamp('concluded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_conclusions');
    }
};
