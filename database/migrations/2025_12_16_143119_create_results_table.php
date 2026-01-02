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
    Schema::create('results', function (Blueprint $table) {
        $table->id();

        $table->foreignId('student_id')->constrained()->cascadeOnDelete();
        $table->foreignId('course_id')->constrained()->cascadeOnDelete();

        $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();

        $table->decimal('percentage', 5, 2); 
        $table->string('grade', 2)->nullable();
        $table->text('comments')->nullable();

        $table->date('assessed_at')->nullable();

        $table->timestamps();

        // one result per student per course
        $table->unique(['student_id', 'course_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
