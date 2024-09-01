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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('connection_type');
            $table->string('facility_type');
            $table->string('cooling_tools');
            $table->string('floor_level');
            $table->string('building')->default('HIRAYA');
            $table->string('remarks');
            $table->string('facility_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
