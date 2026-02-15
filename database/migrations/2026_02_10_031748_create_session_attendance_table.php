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

        Schema::create('session_attendance', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->bigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('class_sessions')->onDelete('cascade');
            $table->date('checked_in_on');
            $table->time('checked_in_at');
            $table->enum('status', ["Present","Tardy"]);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_attendance');
    }
};
