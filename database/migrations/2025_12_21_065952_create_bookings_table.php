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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            // Booking Info
            $table->date('booking_date')->default(now());
            $table->date('expires_at')->nullable()->comment('Booking expired after 3 days');

            // Status
            $table->enum('status', ['pending', 'notified', 'fulfilled', 'cancelled', 'expired'])->default('pending');

            // Priority (dosen gets priority)
            $table->boolean('is_priority')->default(false);

            // Notification
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['book_id', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
