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
        Schema::create('stock_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('monitored_by')->constrained('users')->onDelete('cascade');
            $table->string('no_of_stocks')->nullable();
            $table->string('no_of_stocks_deducted')->nullable();
            $table->string('stocks_left')->nullable();
            $table->string('deducted_at')->nullable();
            $table->string('no_of_stocks_added')->nullable();
            $table->timestamp('added_at')->nullable();    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_monitorings');
    }
};
