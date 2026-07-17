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
        Schema::create('ai_generation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('test_case_templates')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('input_type', ['file', 'text']);
            $table->string('input_filename')->nullable();
            $table->longText('extracted_text')->nullable();

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('proposed_cases')->nullable(); // Résultat brut proposé par l'IA, avant validation humaine
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_generation_requests');
    }
};
