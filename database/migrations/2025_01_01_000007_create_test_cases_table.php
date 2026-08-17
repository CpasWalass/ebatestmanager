<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Auparavant, le statut d'un cas de test vivait dans deux clés d'un JSON libre
 * ("status" et "etat_test"), reconstituées après coup par une fonction PHP qui
 * devinait le sens à partir de mots-clés en français/anglais. Un cas "Bloqué"
 * disparaissait purement et simplement des statistiques.
 *
 * Ici, les DEUX axes deviennent des colonnes réelles, non ambiguës :
 *   - progress : où en est le test dans son cycle de vie (axe "workflow")
 *   - verdict  : ce que le test a donné, uniquement pertinent une fois exécuté (axe "résultat")
 *
 * Le reste des champs (titre, scénarios, résultats obtenus, commentaires...) reste
 * dans `data`, car ils varient selon le template et n'ont pas besoin d'être interrogés
 * pour les KPI.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('test_case_templates')->onDelete('set null');

            // Convention cohérente avec projects.type ('iat' | 'uat'), toujours en minuscules.
            $table->enum('type', ['iat', 'uat'])->default('iat');

            // Axe 1 : progression du test (obligatoire, toujours renseigné)
            $table->enum('progress', ['a_faire', 'en_cours', 'bloque', 'termine'])->default('a_faire');

            // Axe 2 : verdict qualité (rempli seulement quand progress = termine ou bloque)
            $table->enum('verdict', ['valide', 'non_valide', 'sous_reserve', 'optimisation'])->nullable();

            // Validation côté client (UAT), distincte du verdict interne (IAT)
            $table->enum('client_status', ['pending', 'validated', 'rejected'])->default('pending');
            $table->text('client_comment')->nullable();

            $table->enum('source', ['manual', 'excel', 'ai'])->default('manual');

            // Champs libres pilotés par le template (titre, scénarios, résultats...)
            $table->json('data');

            $table->timestamps();

            $table->index(['project_id', 'template_id']);
            $table->index(['project_id', 'progress', 'verdict']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};
