<?php

namespace App\Http\Controllers;

use App\Models\OrgUnit;
use App\Enums\OrgUnitType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrgUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orgUnits = OrgUnit::with('parent')->get();
        return view('org-units.index', compact('orgUnits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $potentialParents = OrgUnit::orderBy('name')->get();
        return view('org-units.create', compact('potentialParents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      =>['required', 'string', 'max:150'],
            'type'      =>['required', new Enum(OrgUnitType::class)],
            'parent_id' =>['nullable', 'exists:org_units,id'],
        ]);

        OrgUnit::create($validated);

        return redirect()->route('org-units.index')
                         ->with('success', 'Unité organisationnelle créée.');
    }

    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrgUnit $orgUnit)
    {
        // Interdire à l'unité d'être son propre parent dans la liste déroulante
        $potentialParents = OrgUnit::where('id', '!=', $orgUnit->id)->orderBy('name')->get();
        
        return view('org-units.edit', compact('orgUnit', 'potentialParents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrgUnit $orgUnit): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:150'],
            'type'      =>['required', new Enum(OrgUnitType::class)],
            // La règle d'or pour éviter la boucle infinie !
            'parent_id' =>['nullable', 'exists:org_units,id', 'not_in:' . $orgUnit->id],
        ]);

        $orgUnit->update($validated);

        return redirect()->route('org-units.index')
                         ->with('success', 'Unité organisationnelle mise à jour.');
    }

    public function destroy(OrgUnit $orgUnit): RedirectResponse
    {
        $orgUnit->delete();

        return redirect()->route('org-units.index')
                         ->with('success', 'Unité supprimée.');
    }
}
