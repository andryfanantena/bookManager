<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'cote_dewey',
        'category_id',
        'titre',
        'auteur',
        'editeur',
        'date_edition',
        'isbn',
        'code_barre',
        'nombre_exemplaires',
        'couverture_couleur',
        'lien_editeur',
        'rayon',
        'chapitres',
        'mots_cles',
        'cover_image',
        'statut',
        'ai_raw_response',
    ];

    protected $casts = [
        'chapitres' => 'array',
        'mots_cles' => 'array',
        'ai_raw_response' => 'array',
        'nombre_exemplaires' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeAVerifier($query)
    {
        return $query->where('statut', 'a_verifier');
    }

    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * URL publique de la couverture.
     *
     * Deux cas possibles :
     * - une URL externe (miniature fournie par Google Books lors du lookup
     *   par ISBN) : on la retourne telle quelle ;
     * - un fichier stocké localement (upload manuel depuis le formulaire de
     *   vérification) : on construit l'URL via le disque "public".
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        return Storage::disk('public')->url($this->cover_image);
    }
}
