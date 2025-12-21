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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Fine Details
            $table->decimal('amount', 10, 2);
            $table->integer('days_overdue');
            $table->decimal('daily_rate', 10, 2)->default(1000)->comment('Rp per hari');

            // Payment
            $table->enum('status', ['unpaid', 'paid', 'waived'])->default('unpaid');
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('paid_at')->nullable();
            $table->foreignId('paid_to')->nullable()->constrained('users')->nullOnDelete();

            // Waive (khusus admin)
            $table->boolean('is_waived')->default(false);
            $table->foreignId('waived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('waive_reason')->nullable();
            $table->timestamp('waived_at')->nullable();

            // Payment Method
            $table->enum('payment_method', ['cash', 'transfer', 'other'])->nullable();
            $table->string('payment_reference')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('loan_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
