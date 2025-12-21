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

            // Basic Information
            $table->string('isbn', 20)->unique()->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('edition')->nullable();

            // Physical Details
            $table->unsignedSmallInteger('pages')->nullable();
            $table->string('language', 5)->default('id');
            $table->string('cover_image')->nullable();

            // Content
            $table->text('description')->nullable();

            // Stock Management
            $table->unsignedInteger('total_stock')->default(0)->comment('Total eksemplar');
            $table->unsignedInteger('available_stock')->default(0)->comment('Yang bisa dipinjam');

            // Location
            $table->string('rack_location', 50)->nullable()->comment('Format: RAK-A-01');

            // Recommendations
            $table->foreignId('recommended_for_major_id')
                ->nullable()
                ->constrained('majors')
                ->nullOnDelete()
                ->comment('Rekomendasi untuk prodi tertentu');

            // Metadata
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('isbn');
            $table->index('rack_location');
            $table->index('is_available');
            $table->fullText(['title', 'author', 'description']);
        });

        // Pivot table untuk many-to-many categories
        Schema::create('book_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['book_id', 'book_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_category');
        Schema::dropIfExists('books');
    }
};
