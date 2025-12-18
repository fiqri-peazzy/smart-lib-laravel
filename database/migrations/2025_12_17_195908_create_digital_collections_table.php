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
        Schema::create('digital_collections', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('title');
            $table->enum('type', ['ebook', 'jurnal', 'skripsi', 'modul', 'paper'])->default('ebook');
            $table->string('author')->nullable();
            $table->year('year')->nullable();
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();

            // File Information
            $table->string('file_path')->comment('Path ke file di storage');
            $table->string('file_type', 10)->comment('pdf, epub, docx, dll');
            $table->unsignedBigInteger('file_size')->comment('Ukuran file dalam bytes');
            $table->string('thumbnail')->nullable()->comment('Cover/thumbnail image');

            // Academic Information (khusus untuk skripsi/paper)
            $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
            $table->string('nim')->nullable()->comment('NIM penulis (untuk skripsi)');
            $table->string('supervisor')->nullable()->comment('Dosen pembimbing');

            // Content & Metadata
            $table->text('description')->nullable();
            $table->text('keywords')->nullable()->comment('Keywords dipisah koma');
            $table->string('language', 5)->default('id')->comment('Bahasa dokumen');

            // Statistics
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);

            // Uploader
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            // Access Control
            $table->boolean('is_public')->default(true)->comment('Akses publik atau terbatas');
            $table->boolean('is_featured')->default(false)->comment('Tampilkan di featured');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('type');
            $table->index('major_id');
            $table->index('year');
            $table->index('is_public');
            $table->fullText(['title', 'author', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_collections');
    }
};
