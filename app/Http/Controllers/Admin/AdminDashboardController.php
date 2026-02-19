<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accident;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Accident::query();

        // Filtre par commune
        if ($request->filled('commune')) {
            $query->where('commune', $request->commune);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('created_at', '>=', $request->date);
        }

     
        // Nombre d’éléments par page (5 par défaut)
        $perPage = $request->get('per_page', 5);

        // Accidents triés du plus récent au plus ancien + pagination
        $accidents = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString(); // conserve les filtres dans l’URL

        // Liste des communes pour le select
        $communes = Accident::select('commune')->distinct()->pluck('commune');

        return view('admin.dashboard', compact('accidents', 'communes'));
    }
}
