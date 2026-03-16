<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = AssetCategory::query();

        // GESTION DU FILTRE (Recherche par nom de la maquette)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // On charge le décompte (pour info) et on pagine (10 par page)
        $categories = $query->withCount('assetTypes')->orderBy('name')->paginate(10);

        return view('asset-categories.index', compact('categories'));
    }

    // CREATE n'est plus utilisé car on gère ça via une modale Alpine sur la page index
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

    // EDIT n'est plus utilisé car on gère ça via une modale Alpine sur la page index
    public function edit(AssetCategory $assetCategory): View
    {
        return view('asset-categories.edit', compact('assetCategory'));
    }

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
        try {
            $assetCategory->delete();
            return redirect()->route('asset-categories.index')->with('success', 'Catégorie supprimée.');
        } catch (\Illuminate\Database\QueryException $e) {
            // SÉCURITÉ MÉTIER : Gestion propre de l'erreur `onDelete('restrict')`
            // Le code 23000 de MySQL indique une violation de contrainte de clé étrangère
            if ($e->getCode() == '23000') {
                return redirect()->route('asset-categories.index')->withErrors(['delete_error' => 'Impossible de supprimer cette catégorie car elle contient des types de matériel.']);
            }
            throw $e; // On relance l'erreur si ce n'est pas une clé étrangère
        }
    }
}