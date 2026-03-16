<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TicketCategoryController extends Controller
{
    /**
     * Affiche la liste des catégories de tickets.
     */
    public function index(Request $request): View
    {
        $query = TicketCategory::query();

        // GESTION DU FILTRE (Recherche par nom)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // On pagine (10 par page) comme demandé sur la maquette
        $categories = $query->orderBy('name')->paginate(10);

        return view('ticket-categories.index', compact('categories'));
    }

    /**
     * Enregistre une nouvelle catégorie.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:ticket_categories,name'],
        ]);

        TicketCategory::create($validated);

        return redirect()->route('ticket-categories.index')
                         ->with('success', 'Catégorie de ticket créée avec succès.');
    }

    /**
     * Met à jour une catégorie existante.
     */
    public function update(Request $request, TicketCategory $ticketCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' =>['required', 'string', 'max:100', 'unique:ticket_categories,name,' . $ticketCategory->id],
        ]);

        $ticketCategory->update($validated);

        return redirect()->route('ticket-categories.index')
                         ->with('success', 'Catégorie mise à jour.');
    }

    /**
     * Supprime une catégorie.
     */
    public function destroy(TicketCategory $ticketCategory): RedirectResponse
    {
        try {
            $ticketCategory->delete();
            return redirect()->route('ticket-categories.index')->with('success', 'Catégorie supprimée.');
        } catch (\Illuminate\Database\QueryException $e) {
            // SÉCURITÉ MÉTIER : On bloque la suppression si des tickets utilisent cette catégorie
            if ($e->getCode() == '23000') {
                return redirect()->route('ticket-categories.index')->withErrors(['delete_error' => 'Impossible de supprimer cette catégorie car des tickets y sont rattachés.']);
            }
            throw $e;
        }
    }
}