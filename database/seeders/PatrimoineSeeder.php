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
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'DLL2024001'],
            [
                'designation' => 'Ordinateur Portable Dell Latitude',
                'description' => 'Ordinateur portable pour développement',
                'categorie' => 'informatique',
                'marque' => 'Dell',
                'modele' => 'Latitude 5520',
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
            ]
        );

        Patrimoine::firstOrCreate(
            ['numero_serie' => 'SAM-S27F350'],
            [
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
            ]
        );

        // Mobilier
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'IKEA-BEKANT-001'],
            [
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
            ]
        );

        // Véhicule
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'TOY2023MGS001'],
            [
                'designation' => 'Voiture de service Toyota Corolla',
                'categorie' => 'vehicule',
                'marque' => 'Toyota',
                'modele' => 'Corolla 2023',
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
            ]
        );

        // Équipement bureau
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'DAIKIN-FTKC35-001'],
            [
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
            ]
        );

        // Bureaux et équipements demandés (fictifs)
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'CHAIR-ERGO-001'],
            [
                'designation' => 'Chaise de bureau ergonomique',
                'categorie' => 'mobilier',
                'marque' => 'OfficeComfort',
                'modele' => 'OC-ERGO',
                'prix_achat' => 120000,
                'date_achat' => now()->subMonths(2),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(2),
                'etat' => 'bon',
                'statut' => 'en_utilisation',
                'localisation' => 'Bureau 1 Ankorondrano',
                'fournisseur' => 'MobilierPro',
                'duree_garantie_mois' => 12,
            ]
        );

        Patrimoine::firstOrCreate(
            ['numero_serie' => 'CHAIR-STD-001'],
            [
                'designation' => 'Chaise de bureau standard',
                'categorie' => 'mobilier',
                'marque' => 'BuroLine',
                'modele' => 'BL-STD',
                'prix_achat' => 80000,
                'date_achat' => now()->subMonths(3),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(3),
                'etat' => 'bon',
                'statut' => 'en_utilisation',
                'localisation' => 'Bureau 2 Ankorondrano',
                'fournisseur' => 'MobilierPro',
                'duree_garantie_mois' => 6,
            ]
        );

        // Ordinateurs portables
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'HP2025MGS001'],
            [
                'designation' => 'Ordinateur Portable HP ProBook',
                'categorie' => 'informatique',
                'marque' => 'HP',
                'modele' => 'ProBook 450',
                'prix_achat' => 2200000,
                'date_achat' => now()->subMonths(1),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(1),
                'etat' => 'neuf',
                'statut' => 'en_utilisation',
                'localisation' => 'Bureau 1 Ankorondrano',
                'fournisseur' => 'TechMad',
                'duree_garantie_mois' => 24,
            ]
        );

        Patrimoine::firstOrCreate(
            ['numero_serie' => 'HP-DESK-002'],
            [
                'designation' => 'Ordinateur de bureau HP',
                'categorie' => 'informatique',
                'marque' => 'HP',
                'modele' => 'EliteDesk',
                'prix_achat' => 1600000,
                'date_achat' => now()->subMonths(5),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(5),
                'etat' => 'bon',
                'statut' => 'en_utilisation',
                'localisation' => 'Bureau 2 Ankorondrano',
                'fournisseur' => 'TechMad',
                'duree_garantie_mois' => 24,
            ]
        );

        // Prise multiple
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'PWR-PM6-001'],
            [
                'designation' => 'Prise multiple 6 prises',
                'categorie' => 'equipement_bureau',
                'marque' => 'PowerMax',
                'modele' => 'PM-6',
                'prix_achat' => 25000,
                'date_achat' => now()->subMonths(1),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(1),
                'etat' => 'neuf',
                'statut' => 'en_utilisation',
                'localisation' => 'Ivato',
                'fournisseur' => 'ElectroMad',
                'duree_garantie_mois' => 6,
            ]
        );

        // Frigo
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'LG-120-FRIG'],
            [
                'designation' => 'Réfrigérateur bureau 120L',
                'categorie' => 'equipement_bureau',
                'marque' => 'LG',
                'modele' => 'LG-120',
                'prix_achat' => 850000,
                'date_achat' => now()->subMonths(2),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(2),
                'etat' => 'neuf',
                'statut' => 'en_utilisation',
                'localisation' => 'Tamatave',
                'fournisseur' => 'ElectroMad',
                'duree_garantie_mois' => 24,
            ]
        );

        // Imprimante
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'CANON-MF244DW'],
            [
                'designation' => 'Imprimante multifonction Canon',
                'categorie' => 'equipement_bureau',
                'marque' => 'Canon',
                'modele' => 'MF244dw',
                'prix_achat' => 420000,
                'date_achat' => now()->subMonths(3),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(3),
                'etat' => 'bon',
                'statut' => 'en_utilisation',
                'localisation' => 'Bureau 1 Ankorondrano',
                'fournisseur' => 'PrintServices',
                'duree_garantie_mois' => 12,
            ]
        );

        // Véhicules
        Patrimoine::firstOrCreate(
            ['numero_serie' => 'TOY-HLX-001'],
            [
                'designation' => 'Véhicule utilitaire Toyota Hilux',
                'categorie' => 'vehicule',
                'marque' => 'Toyota',
                'modele' => 'Hilux 2022',
                'prix_achat' => 120000000,
                'date_achat' => now()->subYear(),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subYear(),
                'etat' => 'bon',
                'statut' => 'en_utilisation',
                'localisation' => 'Parking principal',
                'fournisseur' => 'Toyota Madagascar',
                'duree_garantie_mois' => 36,
            ]
        );

        Patrimoine::firstOrCreate(
            ['numero_serie' => 'YAM125MGS01'],
            [
                'designation' => 'Moto Yamaha 125cc',
                'categorie' => 'vehicule',
                'marque' => 'Yamaha',
                'modele' => 'YBR125',
                'prix_achat' => 6500000,
                'date_achat' => now()->subMonths(6),
                'validateur_id' => $validateur->id,
                'date_validation' => now()->subMonths(6),
                'etat' => 'bon',
                'statut' => 'en_utilisation',
                'localisation' => 'Ivato',
                'fournisseur' => 'MotoMad',
                'duree_garantie_mois' => 12,
            ]
        );

        $this->command->info('Patrimoines créés ou mis à jour avec succès!');
    }
}
