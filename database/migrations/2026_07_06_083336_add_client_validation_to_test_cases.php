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
        Schema::table('test_cases', function (Blueprint $table) {
            $table->enum('client_status', ['pending', 'validated', 'rejected'])->default('pending')->after('type');
            $table->text('client_comment')->nullable()->after('client_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->dropColumn(['client_status', 'client_comment']);
        });
    }
};
