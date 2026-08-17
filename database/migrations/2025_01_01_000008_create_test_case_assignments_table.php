<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_case_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('test_case_templates')->onDelete('cascade');
            $table->foreignId('test_case_id')->nullable()->constrained('test_cases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('scope', ['full_case', 'partial'])->default('full_case');
            $table->json('specific_fields')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_case_assignments');
    }
};
