<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patrimoine;
use App\Models\User;

class PatrimoineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé. Veuillez d\'abord créer des utilisateurs.');
            return;
        }

        $validateur = $users->first();
        $utilisateur = $users->count() > 1 ? $users->skip(1)->first() : $users->first();

        // Ordinateurs
        Patrimoine::create([
            'designation' => 'Ordinateur Portable Dell Latitude',
            'description' => 'Ordinateur portable pour développement',
            'categorie' => 'informatique',
            'marque' => 'Dell',
            'modele' => 'Latitude 5520',
            'numero_serie' => 'DLL2024001',
            'prix_achat' => 3500000,
            'date_achat' => now()->subMonths(6),
            'validateur_id' => $validateur->id,
            'date_validation' => now()->subMonths(6),
            'utilisateur_id' => $utilisateur->id,
            'date_attribution' => now()->subMonths(5),
            'etat' => 'bon',
            'statut' => 'en_utilisation',
            'localisation' => 'Bureau 301',
            'fournisseur' => 'TechMad',
            'duree_garantie_mois' => 24,
        ]);

        Patrimoine::create([
            'designation' => 'Écran Samsung 27 pouces',
            'categorie' => 'informatique',
            'marque' => 'Samsung',
            'modele' => 'S27F350',
            'prix_achat' => 890000,
            'date_achat' => now()->subMonths(8),
            'validateur_id' => $validateur->id,
            'date_validation' => now()->subMonths(8),
            'etat' => 'bon',
            'statut' => 'disponible',
            'localisation' => 'Stock informatique',
            'fournisseur' => 'TechMad',
            'duree_garantie_mois' => 12,
        ]);

        // Mobilier
        Patrimoine::create([
            'designation' => 'Bureau ergonomique',
            'description' => 'Bureau réglable en hauteur',
            'categorie' => 'mobilier',
            'marque' => 'IKEA',
            'modele' => 'BEKANT',
            'prix_achat' => 450000,
            'date_achat' => now()->subYear(),
            'validateur_id' => $validateur->id,
            'date_validation' => now()->subYear(),
            'utilisateur_id' => $utilisateur->id,
            'date_attribution' => now()->subYear(),
            'etat' => 'bon',
            'statut' => 'en_utilisation',
            'localisation' => 'Bureau 301',
            'fournisseur' => 'MobilierPro',
            'duree_garantie_mois' => 36,
        ]);

        // Véhicule
        Patrimoine::create([
            'designation' => 'Voiture de service Toyota Corolla',
            'categorie' => 'vehicule',
            'marque' => 'Toyota',
            'modele' => 'Corolla 2023',
            'numero_serie' => 'TOY2023MGS001',
            'prix_achat' => 85000000,
            'date_achat' => now()->subMonths(10),
            'validateur_id' => $validateur->id,
            'date_validation' => now()->subMonths(10),
            'etat' => 'bon',
            'statut' => 'en_utilisation',
            'localisation' => 'Parking principal',
            'fournisseur' => 'Toyota Madagascar',
            'duree_garantie_mois' => 36,
            'observation' => 'Véhicule pour déplacements professionnels',
        ]);

        // Équipement bureau
        Patrimoine::create([
            'designation' => 'Climatiseur Split 12000 BTU',
            'categorie' => 'equipement_bureau',
            'marque' => 'Daikin',
            'modele' => 'FTKC35',
            'prix_achat' => 2800000,
            'date_achat' => now()->subMonths(4),
            'validateur_id' => $validateur->id,
            'date_validation' => now()->subMonths(4),
            'etat' => 'neuf',
            'statut' => 'en_utilisation',
            'localisation' => 'Salle de réunion',
            'fournisseur' => 'ClimaTech',
            'duree_garantie_mois' => 24,
        ]);

        $this->command->info('5 patrimoines créés avec succès!');
    }
}
