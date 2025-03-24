<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => Category::withCount('supplies')->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'category' => $category,
                'message' => 'Catégorie créée avec succès.'
            ]);
        }

        return back()->with([
            'category' => $category,
            'message' => 'Catégorie créée avec succès.'
        ]);
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Category $category)
    {
        // Trouver ou créer la catégorie "Autre"
        $autreCategory = Category::firstOrCreate(
            ['name' => 'Autre'],
            ['description' => 'Catégorie par défaut pour les fournitures non catégorisées']
        );

        // Transférer toutes les fournitures vers la catégorie "Autre"
        $category->supplies()->update(['category_id' => $autreCategory->id]);

        // Supprimer la catégorie
        $category->delete();

        return back()->with('success', 'Catégorie supprimée avec succès. Les fournitures ont été transférées vers la catégorie "Autre".');
    }
}
