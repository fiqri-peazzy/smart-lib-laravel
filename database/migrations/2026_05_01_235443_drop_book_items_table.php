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
        Schema::dropIfExists('book_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('book_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('barcode', 50)->unique();
            $table->string('qr_code')->nullable();
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('excellent');
            $table->enum('status', ['available', 'on_loan', 'maintenance', 'lost', 'damaged'])->default('available');
            $table->string('current_location', 50)->nullable();
            $table->text('notes')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->decimal('acquisition_price', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('barcode');
            $table->index(['status', 'condition']);
        });
    }
};
