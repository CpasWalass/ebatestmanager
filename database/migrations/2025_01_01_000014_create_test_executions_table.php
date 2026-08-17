<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_case_id')->constrained('test_cases')->onDelete('cascade');
            $table->foreignId('assignment_id')->nullable()->constrained('test_case_assignments')->onDelete('cascade');
            $table->foreignId('tester_id')->constrained('users')->onDelete('cascade');
            $table->json('results')->nullable();
            $table->string('status')->nullable();
            $table->text('comments')->nullable();
            $table->string('nature')->nullable();
            $table->string('priority')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('tenant_id')->nullable()->index();
            $table->timestamps();

            $table->index(['test_case_id', 'tester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_executions');
    }
};
