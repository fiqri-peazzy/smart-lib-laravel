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
            $table->string('qr_code', 50)->unique()->comment('Format: BITEM-YYYY-XXXXX');
            $table->enum('status', ['available', 'on_loan', 'maintenance', 'lost', 'damaged'])->default('available');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'damaged'])->default('excellent');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('qr_code');
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
