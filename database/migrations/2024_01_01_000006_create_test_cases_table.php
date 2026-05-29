<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('test_case_templates')->onDelete('set null');
            $table->json('data'); // Stocke les valeurs des champs selon le template
            $table->timestamps();

            $table->index(['project_id', 'template_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};
