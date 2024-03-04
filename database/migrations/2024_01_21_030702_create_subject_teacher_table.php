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
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();

            $table->foreign('subject_id')->references('id')->on('subjects');//->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('employees');//->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subject_teacher');
    }
};
