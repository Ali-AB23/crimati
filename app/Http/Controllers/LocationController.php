<?php


namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\OrgUnit;
use App\Enums\LocationType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class LocationController extends Controller
{
    public function index(): View
    {
        // On charge chaque lieu AVEC son parent (Eager Loading)
        // et l'Unité Org rattachée (ex: "Service IT")
        $locations = Location::with(['parent', 'orgUnit'])->get();

        return view('locations.index', compact('locations'));
    }

    public function create(): View
    {
        // Pour le formulaire, on a besoin de lister les parents potentiels 
        // et les Unités (Pôle/Service)
        $potentialParents = Location::orderBy('name')->get();
        $orgUnits = OrgUnit::orderBy('name')->get();

        return view('locations.create', compact('potentialParents', 'orgUnits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'type'        =>['required', new Enum(LocationType::class)],
            'parent_id'   =>['nullable', 'exists:locations,id'],
            'org_unit_id' =>['nullable', 'exists:org_units,id'],
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')
                         ->with('success', 'Nouveau lieu créé avec succès.');
    }

     public function show(Location $location): View
    {
        // On charge les sous-lieux (ex: les bureaux d'un étage) et le matériel présent
        $location->load(['parent', 'children', 'orgUnit', 'assets']);

        return view('locations.show', compact('location'));
    }

    public function edit(Location $location): View
    {
        // SÉCURITÉ UI : On exclut le lieu actuel de la liste des parents potentiels !
        $potentialParents = Location::where('id', '!=', $location->id)->orderBy('name')->get();
        $orgUnits = OrgUnit::orderBy('name')->get();

        return view('locations.edit', compact('location', 'potentialParents', 'orgUnits'));
    }


    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'type'        => ['required', new Enum(LocationType::class)],
            
            // SÉCURITÉ BACKEND : 'not_in' empêche formellement le lieu d'être son propre parent
            'parent_id'   => ['nullable', 'exists:locations,id', 'not_in:' . $location->id],
            'org_unit_id' => ['nullable', 'exists:org_units,id'],
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')
                         ->with('success', 'Lieu mis à jour avec succès.');
    }

     public function destroy(Location $location): RedirectResponse
    {
        // NB: Grâce à notre MLD (onDelete('set null')), si on supprime un Bâtiment, 
        // ses Étages ne seront pas effacés, leur parent_id deviendra juste NULL.
        $location->delete();

        return redirect()->route('locations.index')
                         ->with('success', 'Lieu supprimé avec succès.');
    }
}