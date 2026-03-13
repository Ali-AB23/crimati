<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class CheckRole
{
    /**
     * Intercepte la requête entrante.
     * Le paramètre "...$roles" permet de recevoir plusieurs rôles séparés par des virgules.
     */

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Sécurité de base : L'utilisateur est-il connecté ?
        if (!Auth::check()) {
            return redirect()->route('login');
        }


        // 2. On récupère le rôle de l'utilisateur connecté
        // NB: Grâce à notre modèle User, Auth::user()->role est un objet Enum. 
        // On utilise ->value pour récupérer la chaîne de caractères (ex: 'ADMIN_IT')
        $userRole = Auth::user()->role->value;

        // 3. Le rôle de l'utilisateur est-il dans la liste des rôles autorisés pour cette route ?
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 4. Si on arrive ici, l'utilisateur n'a pas le bon rôle.
        // On bloque fermement avec une erreur 403 (Forbidden / Accès Interdit)
        abort(403, "Accès refusé. Votre profil ne vous permet pas d'accéder à cette ressource.");
    }
}
