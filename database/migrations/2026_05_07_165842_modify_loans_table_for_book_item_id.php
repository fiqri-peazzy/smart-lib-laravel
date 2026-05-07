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
        // Karena ini reset data, truncate dulu loans dan turunannya jika perlu
        // Tapi truncate cascade lebih baik dilakukan di seeder. 
        // Kita langsung truncate di sini saja karena skema berubah dan data lama tidak kompatibel.
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('loans')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['book_id']);
            $table->dropColumn('book_id');
            $table->dropColumn('quantity');

            $table->foreignId('book_item_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->index(['book_item_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['book_item_id']);
            $table->dropColumn('book_item_id');

            $table->foreignId('book_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1)->after('book_id');
            $table->index(['book_id', 'status']);
        });
    }
};
