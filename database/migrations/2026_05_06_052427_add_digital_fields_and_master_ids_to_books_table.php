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
        Schema::table('books', function (Blueprint $table) {
            // Master IDs
            $table->foreignId('author_id')->nullable()->after('subtitle')->constrained('authors')->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->after('author_id')->constrained('publishers')->nullOnDelete();

            // Digital Content
            $table->boolean('is_digital')->default(false)->after('publisher_id');
            $table->string('digital_file_path')->nullable()->after('is_digital');
            $table->string('digital_file_type')->nullable()->after('digital_file_path')->comment('ebook, skripsi, paper, etc');
            $table->unsignedBigInteger('digital_file_size')->nullable()->after('digital_file_type');
            $table->unsignedInteger('digital_download_count')->default(0)->after('digital_file_size');
            $table->unsignedInteger('digital_view_count')->default(0)->after('digital_download_count');

            // Academic Info (for digital skripsi/paper)
            $table->text('keywords')->nullable()->after('description');
            $table->string('nim')->nullable()->after('keywords');
            $table->string('supervisor')->nullable()->after('nim');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropForeign(['publisher_id']);
            $table->dropColumn([
                'author_id',
                'publisher_id',
                'is_digital',
                'digital_file_path',
                'digital_file_type',
                'digital_file_size',
                'digital_download_count',
                'digital_view_count',
                'keywords',
                'nim',
                'supervisor',
            ]);
        });
    }

};
