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
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['iat', 'uat'])->default('iat');
            $table->string('tested_version')->nullable();
            $table->date('test_date')->nullable();
            $table->string('responsible')->nullable();
            $table->string('perimeter')->nullable();
            // Stats calculées une seule fois via TestCase::stats() au moment de la génération
            // (même source que les dashboards, plus de calcul divergent).
            $table->json('stats')->nullable();
            $table->text('notes')->nullable();
            $table->json('findings')->nullable();
            $table->string('status')->default('draft'); // draft, sent, retest, acknowledged
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

        Schema::create('report_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
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
