<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // Classification Dewey
            $table->string('cote_dewey')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            // Informations bibliographiques
            $table->string('titre');
            $table->string('auteur')->nullable();
            $table->string('editeur')->nullable();
            $table->string('date_edition')->nullable();
            $table->string('isbn')->nullable()->index();
            $table->string('code_barre')->nullable()->index();
            $table->unsignedInteger('nombre_exemplaires')->default(1);
            $table->string('couverture_couleur')->nullable();
            $table->string('lien_editeur')->nullable();
            $table->string('rayon')->nullable();
            $table->json('chapitres')->nullable();     // ["Tome 1: ...", "Tome 2: ..."]
            $table->json('mots_cles')->nullable();      // mots clés / tags

            // Image de couverture scannée
            $table->string('cover_image')->nullable();

            // Statut du scan : à vérifier après extraction IA, ou validé par l'utilisateur
            $table->enum('statut', ['a_verifier', 'valide'])->default('a_verifier');

            // Réponse brute renvoyée par l'IA (utile pour debug / audit)
            $table->json('ai_raw_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
