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

    // Student basic info
    $table->string('first_name');
    $table->string('last_name');
    $table->string('gender')->nullable(); // male / female / other
    $table->date('date_of_birth')->nullable();

    // Student contacts
    $table->string('student_email')->nullable();
    $table->string('student_phone')->nullable();

    // Parent / guardian contacts
    $table->string('parent_name')->nullable();
    $table->string('parent_email')->nullable();
    $table->string('parent_phone')->nullable();

    // School info
    $table->string('registration_number')->unique();
    $table->year('intake_year')->nullable();

    // Status tracking
    $table->string('status')->default('active'); // active, dropped, graduated
    $table->date('status_date')->nullable();
    $table->text('status_reason')->nullable();

    $table->timestamps();
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
