<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();

        $addedToday = Book::whereDate('created_at', today())->count();

        $totalCategories = Category::count();

        $toVerifyCount = Book::aVerifier()->count();

        // Croissance sur 30 jours (comparaison simple pour le badge +X%)
        $lastMonthCount = Book::where('created_at', '<', now()->subDays(30))->count();
        $growthPercent = $lastMonthCount > 0
            ? round((($totalBooks - $lastMonthCount) / $lastMonthCount) * 100)
            : null;

        // Histogramme des 7 derniers jours
        $weekly = collect(range(6, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);

            return [
                'label' => ucfirst($date->translatedFormat('D')),
                'count' => Book::whereDate('created_at', $date)->count(),
            ];
        });

        $weeklyMax = max(1, $weekly->max('count'));

        // Répartition par rubrique (pour le donut chart)
        $repartition = Category::withCount('books')
            ->orderByDesc('books_count')
            ->limit(6)
            ->get()
            ->map(fn ($cat) => [
                'nom' => $cat->nom,
                'couleur' => $cat->couleur ?? '#0058be',
                'count' => $cat->books_count,
            ]);

        $repartitionTotal = max(1, $repartition->sum('count'));

        $recentBooks = Book::with('category')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'totalBooks' => $totalBooks,
            'addedToday' => $addedToday,
            'totalCategories' => $totalCategories,
            'toVerifyCount' => $toVerifyCount,
            'growthPercent' => $growthPercent,
            'weekly' => $weekly,
            'weeklyMax' => $weeklyMax,
            'repartition' => $repartition,
            'repartitionTotal' => $repartitionTotal,
            'recentBooks' => $recentBooks,
        ]);
    }
}
