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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('version')->nullable()->after('description');
            $table->text('perimeter')->nullable()->after('version');
            // Mettre à jour les statuts de 'planning', 'in_progress', 'in_review', 'completed', 'archived'
            // MySQL n'autorise pas simplement l'ajout d'options ENUM sans récréer.
            // On peut ajouter un champ status_custom ou juste laisser tel quel et utiliser in_review pour "Pour dev"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['version', 'perimeter']);
        });
    }
};
