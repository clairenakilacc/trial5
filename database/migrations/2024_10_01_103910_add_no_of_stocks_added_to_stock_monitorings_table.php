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
        Schema::table('stock_monitorings', function (Blueprint $table) {
            $table->timestamp('added_at')->nullable();    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_monitorings', function (Blueprint $table) {
            $table->dropColumn('no_of_stocks_added'); // Remove the new column
        });
    }
};
