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
        Schema::table('students', function (Blueprint $table) {
            // Employment tracking for Alumni
            $table->boolean('is_employed')->default(false)->after('status');
            $table->string('employment_type')->nullable()->after('is_employed'); // employed, self-employed, internship, etc.
            $table->string('employer_name')->nullable()->after('employment_type');
            $table->string('employer_location')->nullable()->after('employer_name');
            $table->string('job_title')->nullable()->after('employer_location');
            $table->decimal('monthly_salary', 12, 2)->nullable()->after('job_title');
            $table->date('employment_start_date')->nullable()->after('monthly_salary');
            $table->text('career_notes')->nullable()->after('employment_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'is_employed',
                'employment_type',
                'employer_name',
                'employer_location',
                'job_title',
                'monthly_salary',
                'employment_start_date',
                'career_notes'
            ]);
        });
    }
};
