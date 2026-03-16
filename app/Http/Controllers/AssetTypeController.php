<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetTypeController extends Controller
{
    /**
     * Affiche la liste des types avec moteur de recherche.
     */
    public function index(Request $request): View
    {
        $query = AssetType::with('category');

        // Filtre : Nom
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filtre : Specs Template (Avec ou Sans)
        if ($request->filled('has_schema')) {
            if ($request->has_schema === 'yes') {
                // On cherche ceux dont le JSON n'est ni null, ni vide ("[]" ou "{}")
                $query->whereNotNull('spec_schema')
                      ->where('spec_schema', '!=', '[]')
                      ->where('spec_schema', '!=', '{}');
            } elseif ($request->has_schema === 'no') {
                $query->where(function($q) {
                    $q->whereNull('spec_schema')
                      ->orWhere('spec_schema', '[]')
                      ->orWhere('spec_schema', '{}');
                });
            }
        }

        $types = $query->orderBy('name')->paginate(10);

        return view('asset-types.index', compact('types'));
    }

    /**
     * Affiche la PAGE de création (pas de modale ici car le constructeur JSON prend de la place).
     */
    public function create(): View
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('asset-types.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'name'              => ['required', 'string', 'max:100'],
            'spec_schema'       =>['nullable', 'json'], 
        ]);

        $exists = AssetType::where('asset_category_id', $validated['asset_category_id'])
                           ->where('name', $validated['name'])
                           ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ce type existe déjà dans cette catégorie.'])->withInput();
        }

        if (isset($validated['spec_schema'])) {
            $validated['spec_schema'] = json_decode($validated['spec_schema'], true);
        }

        AssetType::create($validated);

        return redirect()->route('asset-types.index')
                         ->with('success', 'Type de matériel créé.');
    }

    /**
     * Affiche la PAGE d'édition.
     */
    public function edit(AssetType $assetType): View
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('asset-types.edit', compact('assetType', 'categories'));
    }

    public function update(Request $request, AssetType $assetType): RedirectResponse
    {
        $validated = $request->validate([
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'name'              => ['required', 'string', 'max:100'],
            'spec_schema'       => ['nullable', 'json'],
        ]);

        $exists = AssetType::where('asset_category_id', $validated['asset_category_id'])
                           ->where('name', $validated['name'])
                           ->where('id', '!=', $assetType->id)
                           ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ce type existe déjà dans cette catégorie.'])->withInput();
        }

        if (isset($validated['spec_schema'])) {
            $validated['spec_schema'] = json_decode($validated['spec_schema'], true);
        }

        $assetType->update($validated);

        return redirect()->route('asset-types.index')
                         ->with('success', 'Type de matériel mis à jour.');
    }

    public function destroy(AssetType $assetType): RedirectResponse
    {
        try {
            $assetType->delete();
            return redirect()->route('asset-types.index')->with('success', 'Type supprimé avec succès.');
        } catch (\Illuminate\Database\QueryException $e) {
            // SÉCURITÉ : Interdire la suppression d'un type ("PC Portable") si un matériel ("066/CRI") l'utilise encore.
            if ($e->getCode() == '23000') {
                return redirect()->route('asset-types.index')->withErrors(['delete_error' => 'Impossible de supprimer ce type car du matériel y est encore rattaché.']);
            }
            throw $e;
        }
    }
}