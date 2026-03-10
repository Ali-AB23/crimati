<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    public function index(): View
    {
        // On charge les catégories avec le nombre de types associés (pratique pour l'affichage)
        $categories = AssetCategory::withCount('assetTypes')->orderBy('name')->get();

        return view('asset-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('asset-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' =>['required', 'string', 'max:100', 'unique:asset_categories,name'],
        ]);

        AssetCategory::create($validated);

        return redirect()->route('asset-categories.index')
                         ->with('success', 'Catégorie créée avec succès.');
    }

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
                return view('asset-categories.edit', compact('assetCategory'));

    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, AssetCategory $assetCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' =>['required', 'string', 'max:100', 'unique:asset_categories,name,' . $assetCategory->id],
        ]);

        $assetCategory->update($validated);

        return redirect()->route('asset-categories.index')
                         ->with('success', 'Catégorie mise à jour.');
    }

   public function destroy(AssetCategory $assetCategory): RedirectResponse
    {
        // Attention : On a mis onDelete('restrict') dans la migration.
        // Si la catégorie contient des types, Laravel/MySQL bloquera la suppression et renverra une exception.
        // Dans un projet avancé, on attraperait l'exception (try/catch) pour afficher un message d'erreur propre.
        $assetCategory->delete();

        return redirect()->route('asset-categories.index')
                         ->with('success', 'Catégorie supprimée.');
    }
}
