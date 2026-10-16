<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table): void {
            // Stable internal identifier, independent of the human-facing number.
            $table->uuid('internal_uid')->nullable()->after('id')->unique();
            // Immutable snapshot of the resident data used to render the document.
            $table->json('data_snapshot')->nullable()->after('payload');
            // Path (on the documents disk) to the frozen PDF captured at issuance.
            $table->string('pdf_path')->nullable()->after('sha256');
            // When this document has been reissued, the document that replaced it.
            $table->foreignId('replaced_by_document_id')->nullable()->after('pdf_path')
                ->constrained('documents')->nullOnDelete();
        });

        Schema::create('document_status_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->string('from_status')->nullable()->comment('One of App\Enums\DocumentStatus');
            $table->string('to_status')->comment('One of App\Enums\DocumentStatus');
            $table->text('reason')->nullable();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_status_events');

        Schema::table('documents', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('replaced_by_document_id');
            $table->dropColumn(['internal_uid', 'data_snapshot', 'pdf_path']);
        });
    }
};
