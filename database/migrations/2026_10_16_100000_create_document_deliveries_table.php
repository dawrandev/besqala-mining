<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A record that a document was dispatched to the resident. With the
        // mailto flow the actual sending happens in the staff member's own mail
        // client; this logs who initiated it, to whom and when (Regulation
        // point 20). It is also the base for future lifecycle notifications.
        Schema::create('document_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->string('channel')->default('email');
            $table->string('to_email');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('dispatched');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['document_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_deliveries');
    }
};
