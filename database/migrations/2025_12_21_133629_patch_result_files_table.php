<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Add index (safe enough in most cases; if it already exists you'll get a duplicate error)
        Schema::table('result_files', function (Blueprint $table) {
            $table->index('result_id');
        });

        // ✅ Change uploaded_by FK behavior to nullOnDelete + make column nullable
        Schema::table('result_files', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
        });

        // NOTE: ->change() requires doctrine/dbal
        Schema::table('result_files', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable()->change();
        });

        Schema::table('result_files', function (Blueprint $table) {
            $table->foreign('uploaded_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('result_files', function (Blueprint $table) {
            $table->dropForeign(['uploaded_by']);
        });

        Schema::table('result_files', function (Blueprint $table) {
            $table->unsignedBigInteger('uploaded_by')->nullable(false)->change();
        });

        Schema::table('result_files', function (Blueprint $table) {
            $table->foreign('uploaded_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::table('result_files', function (Blueprint $table) {
            $table->dropIndex(['result_id']);
        });
    }
};
