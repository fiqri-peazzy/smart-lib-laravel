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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Authentication fields (flexible login)
            $table->string('nim', 20)->unique()->nullable()->comment('NIM untuk mahasiswa/dosen');
            $table->string('username', 50)->unique()->comment('Username untuk login');
            $table->string('email')->unique();
            $table->string('password');

            // Personal information
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('card_number', 30)->nullable()->comment('Nomor kartu mahasiswa/dosen');
            $table->string('avatar')->nullable()->comment('Path foto profil');

            // Academic information
            $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
            $table->year('angkatan')->nullable()->comment('Tahun angkatan');

            // Library system fields
            $table->decimal('credit_score', 5, 2)->default(100.00)->comment('Score 0-100');
            $table->unsignedTinyInteger('max_loans')->default(4)->comment('Maksimal peminjaman dinamis');
            $table->decimal('total_fines', 10, 2)->default(0)->comment('Total denda yang belum dibayar');

            // Status
            $table->enum('status', ['active', 'suspended', 'graduated', 'inactive'])->default('active');

            // Laravel default fields
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performa
            $table->index('nim');
            $table->index('username');
            $table->index('email');
            $table->index('status');
            $table->index('credit_score');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
