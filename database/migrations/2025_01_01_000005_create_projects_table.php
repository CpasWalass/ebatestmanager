<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('version')->nullable();
            $table->text('perimeter')->nullable();

            // Convention unique et cohérente : toujours en minuscules ('iat' | 'uat'),
            // alignée avec test_cases.type pour éviter les comparaisons qui ne matchent jamais.
            $table->enum('type', ['iat', 'uat'])->default('iat');
            $table->enum('status', ['planning', 'in_progress', 'in_review', 'completed', 'archived'])->default('planning');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('links')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'created_by', 'status']);
        });

        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['project_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
        Schema::dropIfExists('projects');
    }
};
