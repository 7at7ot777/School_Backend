<?php
// database/migrations/YYYY_MM_DD_create_subject_teacher_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */


    //TODO: Remoce this migration
    public function up()
    {
        Schema::create('employee_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();


        });
    }

    public function down()
    {
        Schema::dropIfExists('subject_teacher');
    }
};
