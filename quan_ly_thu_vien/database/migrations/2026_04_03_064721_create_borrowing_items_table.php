<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrowing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('returned_quantity')->default(0);
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->enum('status', ['borrowed', 'partially_returned', 'returned', 'overdue'])->default('borrowed');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['borrowing_id', 'book_id']);
            $table->index(['status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowing_items');
    }
};
