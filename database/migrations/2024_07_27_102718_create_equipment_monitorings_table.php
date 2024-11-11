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
        Schema::create('equipment_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade')->nullable()->index('equipmon_equipment_id');
            $table->foreignId('facility_id')->constrained()->onDelete('cascade')->nullable()->index('equipmon_facility_id');
            $table->foreignId('monitored_by')->constrained('users')->onDelete('cascade')->nullable()->index('equipmon_monitored_by');
            $table->text('remarks')->nullable()->nullable()->index('equipmon_remarks');
            $table->string('status')->nullable()->nullable()->index('equipmon_status');
            $table->string('monitored_date')->default(now()->format('M-d-y'))->nullable()->index('equipmon_monitored_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_monitorings');
    }
};