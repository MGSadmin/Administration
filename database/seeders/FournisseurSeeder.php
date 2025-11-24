<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fournisseur;

class FournisseurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fournisseurs = [
            [
                'nom' => 'Comp@gnie Informatique',
                'contact' => 'Rakoto Jean',
                'telephone' => '+261 34 12 345 67',
                'email' => 'contact@compagnie-info.mg',
                'adresse' => 'Lot IVA 123 Antananarivo',
                'nif' => '3001234567',
                'stat' => '56789012345678',
                'type' => 'local',
                'specialites' => 'Matériel informatique, serveurs, réseaux',
                'actif' => true,
            ],
            [
                'nom' => 'Bureau Plus',
                'contact' => 'Ravelo Marie',
                'telephone' => '+261 33 98 765 43',
                'email' => 'info@bureauplus.mg',
                'adresse' => 'Avenue de l\'Indépendance Antananarivo',
                'nif' => '3009876543',
                'stat' => '87654321098765',
                'type' => 'local',
                'specialites' => 'Fournitures de bureau, papeterie',
                'actif' => true,
            ],
            [
                'nom' => 'TechnoMad',
                'contact' => 'Andry Rasoanaivo',
                'telephone' => '+261 32 45 678 90',
                'email' => 'ventes@technomad.mg',
                'adresse' => 'Zone industrielle Forello Tanjombato',
                'nif' => '3005555555',
                'stat' => '55555555555555',
                'type' => 'local',
                'specialites' => 'Électronique, équipements IT, accessoires',
                'actif' => true,
            ],
            [
                'nom' => 'MobilPro Madagascar',
                'contact' => 'Hery Ratsim',
                'telephone' => '+261 34 77 88 99',
                'email' => 'contact@mobilpro.mg',
                'adresse' => 'Ankorondrano Antananarivo',
                'nif' => '3007777777',
                'stat' => '77777777777777',
                'type' => 'local',
                'specialites' => 'Mobilier de bureau, aménagement',
                'actif' => true,
            ],
            [
                'nom' => 'Dell Technologies',
                'contact' => 'International Sales',
                'telephone' => '+33 1 23 45 67 89',
                'email' => 'sales@dell.com',
                'adresse' => 'One Dell Way, Round Rock, Texas, USA',
                'type' => 'international',
                'specialites' => 'Ordinateurs, serveurs, stockage',
                'actif' => true,
            ],
        ];

        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::create($fournisseur);
        }

        $this->command->info('✅ ' . count($fournisseurs) . ' fournisseurs créés avec succès');
    }
}

