<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_case_templates', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name')->default('Défaut');
            // Uniquement les champs "métier" libres (titre, scénarios, résultats attendus...).
            // Le statut du cas de test N'EST PLUS dans ce JSON : voir test_cases.progress / test_cases.verdict.
            $table->json('fields');
            $table->json('links')->nullable();
            $table->timestamps();

            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_case_templates');
    }
};
