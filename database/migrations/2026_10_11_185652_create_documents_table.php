<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table): void {
            $table->id();
            // Verifiable number, e.g. "G-2026-0001".
            $table->string('number')->unique();
            $table->string('kind')->comment('One of App\Enums\DocumentKind');
            $table->string('status')->default('active')->comment('One of App\Enums\DocumentStatus');
            $table->foreignId('resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('applications')->nullOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            // Rendered content / fields captured at issue time.
            $table->json('payload')->nullable();
            $table->string('template_version')->nullable();
            $table->string('sha256', 64)->nullable()->comment('Integrity hash of the generated PDF');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('kind');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
