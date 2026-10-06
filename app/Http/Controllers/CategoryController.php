<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('nom')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories,nom'],
            'couleur' => ['nullable', 'string', 'max:20'],
        ]);

        Category::create($validated);

        return back()->with('success', 'Rubrique ajoutée.');
    }

    public function destroy(Category $category)
    {
        $category->delete(); // les livres liés passent en category_id = null (nullOnDelete)

        return back()->with('success', 'Rubrique supprimée.');
    }
}
