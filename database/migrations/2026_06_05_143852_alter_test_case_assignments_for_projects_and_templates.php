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
        Schema::table('test_case_assignments', function (Blueprint $table) {
            $table->dropForeign(['test_case_id']);
            $table->dropUnique(['test_case_id', 'user_id']);
            $table->foreign('test_case_id')->references('id')->on('test_cases')->onDelete('cascade');
            
            $table->foreignId('project_id')->nullable()->after('id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->after('project_id')->constrained('test_case_templates')->onDelete('cascade');
            $table->unsignedBigInteger('test_case_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_case_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('test_case_id')->nullable(false)->change();
            $table->dropForeign(['project_id']);
            $table->dropForeign(['template_id']);
            $table->dropColumn(['project_id', 'template_id']);
            $table->unique(['test_case_id', 'user_id']);
        });
    }
};
