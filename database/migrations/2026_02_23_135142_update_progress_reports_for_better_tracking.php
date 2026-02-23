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
        Schema::table('progress_reports', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->constrained()->nullOnDelete()->after('student_id');
            // Adding a rating/level system if not already there correctly
            $table->string('progress_level')->nullable()->after('report_date'); // excellent, good, average, needs_improvement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('enrollment_id');
            $table->dropColumn('progress_level');
        });
    }
};
