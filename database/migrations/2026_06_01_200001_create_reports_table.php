<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('iat'); // iat, uat
            $table->string('tested_version')->nullable();
            $table->date('test_date')->nullable();
            $table->string('responsible')->nullable(); // Nom du responsable
            $table->string('perimeter')->nullable(); // Périmètre du test
            $table->json('stats')->nullable(); // { success, failure, reserve, optimisation, total }
            $table->text('notes')->nullable(); // NB / commentaire général
            $table->json('sections')->nullable(); // sections personnalisées
            $table->string('status')->default('draft'); // draft, sent, acknowledged
            $table->string('tenant_id')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index('created_by');
        });

        Schema::create('report_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content'); // ce que le développeur a corrigé
            $table->string('status')->default('in_progress'); // in_progress, done
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_responses');
        Schema::dropIfExists('reports');
    }
};
