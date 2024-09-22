<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBorrowedAndReturnedDateColumnsToString extends Migration
{
    public function up()
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->string('borrowed_date')->nullable()->change();
            $table->string('returned_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->date('borrowed_date')->nullable()->change();
            $table->date('returned_date')->nullable()->change();
        });
    }
}
