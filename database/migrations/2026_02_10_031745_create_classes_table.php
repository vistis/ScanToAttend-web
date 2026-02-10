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
        Schema::disableForeignKeyConstraints();

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->string('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->smallInteger('section');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
