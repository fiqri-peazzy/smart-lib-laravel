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
        Schema::create('book_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            // Unique Identifier
            $table->string('barcode', 50)->unique()->comment('Format: BOOK-YYYY-XXXXX');
            $table->string('qr_code')->nullable()->comment('Path to QR code image');

            // Condition & Status
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])
                ->default('excellent');
            $table->enum('status', ['available', 'on_loan', 'maintenance', 'lost', 'damaged'])
                ->default('available');

            // Location (could be different from main book location)
            $table->string('current_location', 50)->nullable();

            // Notes
            $table->text('notes')->nullable()->comment('Catatan kondisi atau riwayat');

            // Acquisition
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_price', 10, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('barcode');
            $table->index('status');
            $table->index(['book_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_items');
    }
};
