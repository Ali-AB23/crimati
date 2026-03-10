<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OrgUnit;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with(['user', 'orgUnit', 'officeLocation'])->get();

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orgUnits = OrgUnit::orderBy('name')->get();
        // On ne liste que les lieux de type "Bureau" (OFFICE)
        $officeLocations = Location::where('type', 'OFFICE')->orderBy('name')->get();

        return view('employees.create', compact('orgUnits', 'officeLocations'));
    }

    /**
     * Sauvegarde un employé.
     * ATTENTION : Dans un projet réel, on crée le `User` ET le `Employee` en même temps.
     * Pour ce contrôleur simple, on part du principe que le User est géré séparément.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'            =>['required', 'exists:users,id', 'unique:employees,user_id'],
            'matricule'          =>['required', 'string', 'unique:employees,matricule'],
            'full_name'          =>['required', 'string', 'max:150'],
            'email'              => ['nullable', 'email', 'unique:employees,email'],
            'phone'              => ['nullable', 'string', 'max:30'],
            'org_unit_id'        => ['required', 'exists:org_units,id'],
            'office_location_id' => ['nullable', 'exists:locations,id'],
        ]);

        Employee::create($validated);

        return redirect()->route('employees.index')
                         ->with('success', 'Employé ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): View
    {
        // On charge l'unité, le bureau, et tout le matériel affecté à cet employé !
        // C'est ici qu'on gère la vue "Matériels de cet employé" demandée dans le CDC.
        $employee->load(['orgUnit', 'officeLocation']);
        
        // On récupère les matériels où current_employee_id = $employee->id
        $assets = \App\Models\Asset::where('current_employee_id', $employee->id)
                                   ->with(['type', 'currentLocation'])
                                   ->get();

        return view('employees.show', compact('employee', 'assets'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee): View
    {
        $orgUnits = OrgUnit::orderBy('name')->get();
        $officeLocations = Location::where('type', 'OFFICE')->orderBy('name')->get();

        return view('employees.edit', compact('employee', 'orgUnits', 'officeLocations'));
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            // On ignore l'employé actuel pour la validation d'unicité
            'matricule'          =>['required', 'string', 'unique:employees,matricule,' . $employee->id],
            'full_name'          =>['required', 'string', 'max:150'],
            'email'              =>['nullable', 'email', 'unique:employees,email,' . $employee->id],
            'phone'              =>['nullable', 'string', 'max:30'],
            'org_unit_id'        =>['required', 'exists:org_units,id'],
            'office_location_id' =>['nullable', 'exists:locations,id'],
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')
                         ->with('success', 'Fiche employé mise à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        // Selon notre base, les matériels affectés à cet employé verront 
        // leur 'current_employee_id' passer à NULL automatiquement (set null).
        $employee->delete();

        return redirect()->route('employees.index')
                         ->with('success', 'Employé retiré du système.');
    }
}
