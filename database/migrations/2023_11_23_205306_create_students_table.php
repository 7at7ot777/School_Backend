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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('grade_level');
            $table->unsignedBigInteger('father_id');
            $table->unsignedBigInteger('mother_id');
            $table->unsignedBigInteger('class_id');
            $table->enum('semester', [1, 2, 3]);
            $table->timestamps();
            //$table->boolean('is_active')->default(true);
            //$table->foreign('class_id')->references('id')->on('class_rooms');
            //$table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
