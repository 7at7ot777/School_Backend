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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
//            $table->boolean('is_active')->default(true);
//            $table->unsignedBigInteger('role_id');
//            $table->foreign('role_id')->references('id')->on('roles');
            $table->enum('role', ['admin','superAdmin','employee','teacher']); //roles have not been determined yet
            $table->unsignedBigInteger('department_id')->nullable(); // Foreign key
            $table->integer('basic_salary')->default(0)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
