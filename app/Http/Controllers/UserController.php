<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrgUnit;
use App\Models\Location;
use App\Enums\UserRole;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Liste combinée Users/Employees (Maquette)
     */
    public function index(Request $request): View
    {
        // On part du User, on charge l'employé et ses relations RH
        $query = User::with(['employee.orgUnit', 'employee.officeLocation']);

        // 1. FILTRE "Query" (Matricule, Nom, ou Username)
        if ($request->filled('query')) {
            $searchTerm = '%' . $request->query('query') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('username', 'like', $searchTerm)
                  // On fait une sous-requête dans la table employees
                  ->orWhereHas('employee', function($empQuery) use ($searchTerm) {
                      $empQuery->where('full_name', 'like', $searchTerm)
                               ->orWhere('matricule', 'like', $searchTerm);
                  });
            });
        }

        // 2. FILTRE "Role"
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 3. FILTRE "Active"
        if ($request->filled('active')) {
            // '1' pour actif, '0' pour inactif
            $isActive = $request->active === '1' ? true : false;
            $query->where('active', $isActive);
        }

        // Exécution de la requête paginée
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        $roles = UserRole::cases();

        return view('users.index', compact('users', 'roles'));
    }
     public function create(): View
    {
        $roles = UserRole::cases();
        $orgUnits = OrgUnit::orderBy('name')->get();
        // L'Admin ne devrait pouvoir assigner un employé qu'à un vrai bureau physique.
        $officeLocations = Location::where('type', \App\Enums\LocationType::OFFICE->value)->orderBy('name')->get();

        return view('users.create', compact('roles', 'orgUnits', 'officeLocations'));
    }

    /**
     * Sauvegarde le nouvel utilisateur ET son profil employé.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation croisée des deux tables
        $validated = $request->validate([
            // Table USERS
            'username'           =>['required', 'string', 'max:50', 'unique:users,username'],
            'role'               => ['required', new Enum(UserRole::class)],
            'password'           => ['required', 'string', 'min:8', 'confirmed'], // Exige un champ password_confirmation
            'active'             => ['boolean'],
            
            // Table EMPLOYEES
            'matricule'          =>['required', 'string', 'max:50', 'unique:employees,matricule'],
            'full_name'          =>['required', 'string', 'max:150'],
            'email'              =>['nullable', 'email', 'max:150', 'unique:employees,email'],
            'phone'              =>['nullable', 'string', 'max:30'],
            'org_unit_id'        =>['required', 'exists:org_units,id'],
            'office_location_id' =>['nullable', 'exists:locations,id'],
        ]);

        try {
            // 2. Transaction pour garantir l'intégrité (Tout ou Rien)
            DB::transaction(function () use ($validated,$request) {
                
                // A. Création du compte de connexion
                $user = User::create([
                    'username' => $validated['username'],
                    'password' => Hash::make($validated['password']),
                    'role'     => $validated['role'],
                    'active'   => $request->has('active'), // Si la case est cochée, c'est true
                ]);

                // B. Création du profil RH lié
                Employee::create([
                    'user_id'            => $user->id,
                    'matricule'          => $validated['matricule'],
                    'full_name'          => $validated['full_name'],
                    'email'              => $validated['email'],
                    'phone'              => $validated['phone'],
                    'org_unit_id'        => $validated['org_unit_id'],
                    'office_location_id' => $validated['office_location_id'],
                ]);
            });

            return redirect()->route('users.index')->with('success', 'Utilisateur et profil RH créés avec succès.');

        } catch (\Exception $e) {
            // Si la transaction plante (très rare si la validation passe), on revient en arrière
            return back()->withErrors(['create_error' => 'Une erreur est survenue lors de la création : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(User $user): View
    {
        // On charge l'employé associé pour pré-remplir le formulaire
        $user->load('employee');

        $roles = \App\Enums\UserRole::cases();
        $orgUnits = OrgUnit::orderBy('name')->get();
        $officeLocations = Location::where('type', \App\Enums\LocationType::OFFICE->value)->orderBy('name')->get();

        return view('users.edit', compact('user', 'roles', 'orgUnits', 'officeLocations'));
    }

    /**
     * Met à jour l'utilisateur ET son profil RH.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // On récupère l'ID de l'employé associé (pour la validation d'unicité)
        $employeeId = $user->employee ? $user->employee->id : null;

        $validated = $request->validate([
            // Table USERS : On ignore l'ID du user actuel pour l'unicité
            'username'           => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'role'               => ['required', new Enum(UserRole::class)],
            // LE MOT DE PASSE DEVIENT NULLABLE ! S'il est vide, on ne le modifie pas.
            'password'           =>['nullable', 'string', 'min:8', 'confirmed'], 
            'active'             => ['boolean'],
            
            // Table EMPLOYEES : On ignore l'ID de l'employé actuel
            'matricule'          =>['required', 'string', 'max:50', 'unique:employees,matricule,' . $employeeId],
            'full_name'          => ['required', 'string', 'max:150'],
            'email'              =>['nullable', 'email', 'max:150', 'unique:employees,email,' . $employeeId],
            'phone'              =>['nullable', 'string', 'max:30'],
            'org_unit_id'        => ['required', 'exists:org_units,id'],
            'office_location_id' => ['nullable', 'exists:locations,id'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $user) {
                
                // A. Mise à jour des infos de connexion
                $userData = [
                    'username' => $validated['username'],
                    'role'     => $validated['role'],
                    'active'   => $request->has('active'),
                ];

                // On ne met à jour le mot de passe QUE s'il a été rempli
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $user->update($userData);

                // B. Mise à jour du profil RH
                if ($user->employee) {
                    $user->employee->update([
                        'matricule'          => $validated['matricule'],
                        'full_name'          => $validated['full_name'],
                        'email'              => $validated['email'],
                        'phone'              => $validated['phone'],
                        'org_unit_id'        => $validated['org_unit_id'],
                        'office_location_id' => $validated['office_location_id'],
                    ]);
                }
            });

            return redirect()->route('users.index')->with('success', 'Le profil a été mis à jour avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors(['update_error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Active ou désactive un utilisateur (Toggle Active Modale)
     */
    public function toggleActive(User $user): RedirectResponse
    {
        // Sécurité : On ne peut pas se désactiver soi-même !
        if ($user->id === \Illuminate\Support\Facades\Auth::id()) {
            return back()->withErrors(['toggle_error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        // On inverse simplement le booléen
        $user->update([
            'active' => !$user->active
        ]);

        $status = $user->active ? 'activé' : 'désactivé';
        return back()->with('success', "Le compte de {$user->username} a été {$status}.");
    }

    // (Je ne mets pas les méthodes create/edit ici, on les fera dans la prochaine étape !)
}