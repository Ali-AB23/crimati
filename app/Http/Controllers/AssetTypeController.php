<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetTypeController extends Controller
{
    public function index(): View
    {
        $types = AssetType::with('category')->orderBy('name')->get();

        return view('asset-types.index', compact('types'));
    }

    public function create(): View
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('asset-types.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'name'              =>['required', 'string', 'max:100'],
            // On s'assure que si l'admin remplit les specs, c'est bien du format JSON valide !
            'spec_schema'       =>['nullable', 'json'], 
        ]);

        // Vérification de l'index unique composite (catégorie + nom)
        $exists = AssetType::where('asset_category_id', $validated['asset_category_id'])
                           ->where('name', $validated['name'])
                           ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ce type existe déjà dans cette catégorie.'])->withInput();
        }

        // Si spec_schema est envoyé sous forme de chaîne JSON, Laravel va le stocker tel quel,
        // et le convertira en array quand on le lira grâce à notre $casts =['spec_schema' => 'array'].
        if (isset($validated['spec_schema'])) {
            $validated['spec_schema'] = json_decode($validated['spec_schema'], true);
        }

        AssetType::create($validated);

        return redirect()->route('asset-types.index')
                         ->with('success', 'Type de matériel créé.');
    }

    public function edit(AssetType $assetType): View
    {
        $categories = AssetCategory::orderBy('name')->get();
        return view('asset-types.edit', compact('assetType', 'categories'));
    }

    public function update(Request $request, AssetType $assetType): RedirectResponse
    {
        $validated = $request->validate([
            'asset_category_id' =>['required', 'exists:asset_categories,id'],
            'name'              =>['required', 'string', 'max:100'],
            'spec_schema'       => ['nullable', 'json'],
        ]);

        // Vérification des doublons (en excluant le type actuel)
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
        $assetType->delete();

        return redirect()->route('asset-types.index')
                         ->with('success', 'Type supprimé.');
    }
}