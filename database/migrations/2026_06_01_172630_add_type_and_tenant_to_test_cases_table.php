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
            if (!Schema::hasColumn('test_cases', 'tenant_id')) {
                $table->string('tenant_id')->nullable()->index();
            }
            if (!Schema::hasColumn('test_cases', 'type')) {
                $table->string('type')->default('iat'); // 'iat' ou 'uat'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->dropColumn(['tenant_id', 'type']);
        });
    }
};
