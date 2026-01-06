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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Course timing per student
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Enrollment status
            $table->string('status')->default('active'); // active, completed, dropped
            $table->date('status_date')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Prevent duplicate enrollment in same course
            $table->unique(['student_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
