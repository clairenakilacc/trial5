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
        Schema::create('request_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            //$table->foreignId('equipment_id')->nullable()->change()->constrained()->onDelete('cascade');           
            $table->foreignId('equipment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->date('date')->nullable();
            $table->string('purpose')->nullable();
            $table->string('start_date_and_time_of_use')->nullable();
            $table->string('end_date_and_time_of_use')->nullable();
            $table->string('expected_return_date')->nullable();
            $table->string('college_department_office')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_lists');
    }
};
