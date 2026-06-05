<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_executions', function (Blueprint $table) {
            $table->string('nature')->nullable()->after('status');
            $table->string('priority')->nullable()->after('nature'); // P1, P2, P3
            $table->timestamp('started_at')->nullable()->after('priority');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('test_executions', function (Blueprint $table) {
            $table->dropColumn(['nature', 'priority', 'started_at', 'completed_at']);
        });
    }
};
