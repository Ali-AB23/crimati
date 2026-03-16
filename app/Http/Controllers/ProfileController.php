<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\OrgUnit;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('employee'); // S'assure que les infos RH sont chargées

        // Pour les listes déroulantes de l'affectation
        $orgUnits = OrgUnit::orderBy('name')->get();
        $officeLocations = Location::where('type', \App\Enums\LocationType::OFFICE->value)->orderBy('name')->get();

        return view('profile.index',[ // <-- On utilise bien index.blade.php comme défini dans notre arborescence
            'user' => $user,
            'orgUnits' => $orgUnits,
            'officeLocations' => $officeLocations,
        ]);
    }

    /**
     * Update the user's profile information.
     * Note: Breeze utilise ProfileUpdateRequest. On l'utilise juste pour la forme,
     * mais on va faire notre propre validation métier ici.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $employeeId = $user->employee ? $user->employee->id : null;

        // 1. Validation de nos champs spécifiques (Users + Employees)
        $validated = $request->validate([
            // Seul l'Admin devrait pouvoir changer le matricule, mais on le met en lecture seule dans la vue pour l'employé.
            // On le valide quand même si on le reçoit.
            'full_name'          => ['required', 'string', 'max:150'],
            'email'              =>['nullable', 'email', 'max:150', 'unique:employees,email,' . $employeeId],
            'phone'              => ['nullable', 'string', 'max:30'],
            'org_unit_id'        => ['required', 'exists:org_units,id'],
            'office_location_id' => ['nullable', 'exists:locations,id'],
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                // Mise à jour de la table Employee
                if ($user->employee) {
                    $user->employee->update([
                        'full_name'          => $validated['full_name'],
                        'email'              => $validated['email'],
                        'phone'              => $validated['phone'],
                        'org_unit_id'        => $validated['org_unit_id'],
                        'office_location_id' => $validated['office_location_id'],
                    ]);
                }
            });

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (\Exception $e) {
            return back()->withErrors(['update_error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}