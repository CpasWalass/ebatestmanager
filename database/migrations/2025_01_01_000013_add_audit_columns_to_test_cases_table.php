<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->foreignId('verdict_by')->nullable()->constrained('users')->nullOnDelete()->after('verdict');
            $table->timestamp('verdict_set_at')->nullable()->after('verdict_by');
            $table->foreignId('progress_by')->nullable()->constrained('users')->nullOnDelete()->after('verdict_set_at');
            $table->foreignId('client_status_by')->nullable()->constrained('users')->nullOnDelete()->after('client_comment');
        });
    }

    public function down(): void
    {
        Schema::table('test_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verdict_by');
            $table->dropColumn('verdict_set_at');
            $table->dropConstrainedForeignId('progress_by');
            $table->dropConstrainedForeignId('client_status_by');
        });
    }
};
