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
        Schema::create('facility_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade')->nullable()->index('facmon_facility_id');
            $table->foreignId('monitored_by')->constrained('users')->onDelete('cascade')->nullable()->index('facmon_monitored_by');
            $table->string('monitored_date')->nullable()->nullable()->index('facmon_monitored_date');
            $table->text('remarks')->nullable()->nullable()->index('facmon_remarks');
            // $table->string('facility_img')->nullable();
            $table->timestamps();
        });
    }

     /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_monitorings');
    }
};