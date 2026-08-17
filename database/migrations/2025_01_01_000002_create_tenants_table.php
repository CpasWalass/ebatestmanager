<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table minimale conservée pour ne pas fermer la porte à du multi-tenant
 * plus tard, mais SANS résolution dynamique (domaine/sous-domaine/header).
 * Un seul tenant réel existe pour l'instant : voir TenantSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
