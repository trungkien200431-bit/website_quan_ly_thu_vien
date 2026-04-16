<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('reader_id')->constrained('readers')->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_borrowing_id')->nullable()->constrained('borrowings')->nullOnDelete();
            $table->date('request_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('note')->nullable();
            $table->text('processed_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'request_date']);
        });

        Schema::create('borrowing_request_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('borrowing_request_id')->constrained('borrowing_requests')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['borrowing_request_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_request_items');
        Schema::dropIfExists('borrowing_requests');
    }
};
