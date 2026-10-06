<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Généralités / Informatique', 'couleur' => '#0058be'],
            ['nom' => 'Philosophie', 'couleur' => '#2170e4'],
            ['nom' => 'Religion', 'couleur' => '#5b8def'],
            ['nom' => 'Sciences sociales', 'couleur' => '#b6c4ff'],
            ['nom' => 'Langues', 'couleur' => '#90a8ff'],
            ['nom' => 'Sciences', 'couleur' => '#3d4143'],
            ['nom' => 'Technologie', 'couleur' => '#757682'],
            ['nom' => 'Arts', 'couleur' => '#c4c7c9'],
            ['nom' => 'Littérature / Roman', 'couleur' => '#00236f'],
            ['nom' => 'Histoire / Géographie', 'couleur' => '#e0e3e5'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['nom' => $category['nom']], $category);
        }
    }
}
