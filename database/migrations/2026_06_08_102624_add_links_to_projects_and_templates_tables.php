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
        Schema::table('projects', function (Blueprint $table) {
            $table->json('links')->nullable()->after('type');
        });

        Schema::table('test_case_templates', function (Blueprint $table) {
            $table->json('links')->nullable()->after('fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_case_templates', function (Blueprint $table) {
            $table->dropColumn('links');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('links');
        });
    }
};
