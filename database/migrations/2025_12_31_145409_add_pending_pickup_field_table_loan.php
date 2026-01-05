<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('requested_at')->nullable()->after('loan_date');
            $table->timestamp('pickup_deadline')->nullable()->after('requested_at');
        });

        DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('pending_pickup', 'active', 'extended', 'overdue', 'returned') NOT NULL DEFAULT 'active'");
    }

    /**s
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['requested_at', 'pickup_deadline']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('active', 'extended', 'overdue', 'returned') NOT NULL DEFAULT 'active'");
    }
};