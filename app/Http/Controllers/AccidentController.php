<?php

namespace App\Http\Controllers;

use App\Models\Accident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Exception;

class AccidentController extends Controller
{
    /**
     * Affiche le formulaire d'enregistrement d'accident
     */
    public function create()
    {
         $departements = [
                'Alibori' => [
                    'Banikoara', 'Gogounou', 'Kandi', 'Karimama', 'Malanville', 'Ségbana'
                ],
                'Atacora' => [
                    'Boukoumbé', 'Cobly', 'Kérou', 'Kouandé', 'Matéri', 'Natitingou', 'Péhunco', 'Tanguiéta', 'Toucountouna'
                ],
                'Atlantique' => [
                    'Abomey-Calavi', 'Allada', 'Kpomassè', 'Ouidah', 'Sô-Ava', 'Toffo', 'Tori-Bossito', 'Zè'
                ],
                'Borgou' => [
                    'Bembèrèkè', 'Kalalé', 'N\'Dali', 'Nikki', 'Parakou', 'Pèrèrè', 'Sinendé', 'Tchaourou'
                ],
                'Collines' => [
                    'Bantè', 'Dassa-Zoumè', 'Glazoué', 'Ouèssè', 'Savalou', 'Savè'
                ],
                'Couffo' => [
                    'Aplahoué', 'Djakotomey', 'Dogbo', 'Klouékanmè', 'Lalo', 'Toviklin'
                ],
                'Donga' => [
                    'Bassila', 'Copargo', 'Djougou', 'Ouaké'
                ],
                'Littoral' => [
                    'Cotonou'
                ],
                'Mono' => [
                    'Athiémé', 'Bopa', 'Comè', 'Grand-Popo', 'Houéyogbé', 'Lokossa'
                ],
                'Ouémé' => [
                    'Adjarra', 'Adjohoun', 'Aguégués', 'Akpro-Missérété', 'Avrankou', 'Bonou', 'Dangbo', 'Porto-Novo', 'Sèmè-Podji'
                ],
                'Plateau' => [
                    'Adja-Ouèrè', 'Ifangni', 'Kétou', 'Pobè', 'Sakété'
                ],
                'Zou' => [
                    'Abomey', 'Agbangnizoun', 'Bohicon', 'Covè', 'Djidja', 'Ouinhi', 'Zagnanado', 'Za-Kpota', 'Zogbodomey'
                ],
  ] ;       
        return view('formulaire', ['departements' => $departements]);
    }

    /**
     * Enregistre un nouvel accident dans la base de données
     */
     public function store(Request $request)
    {
        // Validation des données de base
        $validator = Validator::make($request->all(), [
            'commune' => 'required|string|max:255',
            'lieu' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'altitude' => 'nullable|numeric',
            'precision' => 'nullable|numeric',
            'photos' => 'nullable|array',
            'nombre_vehicules' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Traitement des photos en base64
        $photoPaths = [];
        if ($request->has('photos')) {
            foreach ($request->input('photos') as $index => $photoData) {
                if ($photoData && preg_match('/^data:image\/(\w+);base64,/', $photoData, $matches)) {
                    try {
                        // Extraire le type d'image et les données base64
                        $imageType = $matches[1];
                        $imageData = substr($photoData, strpos($photoData, ',') + 1);
                        $imageData = base64_decode($imageData);

                        // Valider que c'est une image valide
                      

                        // Générer un nom de fichier unique
                        $fileName = time() . '_' . $index . '.' . $imageType;
                        $path = 'accidents/' . $fileName;

                        // Enregistrer le fichier
                        Storage::disk('public')->put($path, $imageData);
                        $photoPaths[] = $path;
                    } catch (\Exception $e) {
                        // Ignorer les erreurs de traitement d'image
                        continue;
                    }
                }
            }
        }

        // Conversion des valeurs vides en null
        $altitude = $request->filled('altitude') ? $request->input('altitude') : null;
        $precisionValue = $request->filled('precision') ? $request->input('precision') : null;

        // Création de l'enregistrement
        $accident = Accident::create([
            'commune' => $request->input('commune'),
            'lieu' => $request->input('lieu'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'altitude' => $altitude,
            'precision' => $precisionValue,
            'photos' => !empty($photoPaths) ? json_encode($photoPaths) : null,
            'nombre_vehicules' => $request->nombre_vehicules,

        ]);

        return redirect()->back()->with('success', 'Accident enregistré avec succès! ID: ' . $accident->id);
    }

    /**
     * Affiche la liste des accidents
     */
    public function index()
    {
        $accidents = Accident::orderBy('created_at', 'desc')->get();
        return view('accidents.index', compact('accidents'));
    }

    /**
     * Affiche les détails d'un accident
     */
    public function show(Accident $accident)
    {
        return view('accidents.show', compact('accident'));
    }
}