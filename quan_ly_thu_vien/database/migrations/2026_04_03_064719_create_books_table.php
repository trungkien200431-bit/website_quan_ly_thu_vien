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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn')->nullable()->unique();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('published_year')->nullable();
            $table->string('shelf_location')->nullable();
            $table->unsignedInteger('total_copies')->default(0);
            $table->unsignedInteger('available_copies')->default(0);
            $table->decimal('price', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
