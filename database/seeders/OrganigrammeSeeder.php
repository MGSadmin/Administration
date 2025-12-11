<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;
use App\Models\OrganizationMember;

class OrganigrammeSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les départements principaux
        $dg = Department::create([
            'name' => 'Direction Générale',
            'code' => 'DG',
            'description' => 'Direction Générale du Groupe TLT',
            'color' => '#667eea',
            'order' => 1,
        ]);

        $dirCom = Department::create([
            'name' => 'Direction Commerciale',
            'code' => 'DIR_COM',
            'description' => 'Direction Commerciale',
            'color' => '#f6993f',
            'parent_id' => $dg->id,
            'order' => 1,
        ]);

        $dirOps = Department::create([
            'name' => 'Direction des Opérations',
            'code' => 'DIR_OPS',
            'description' => 'Direction des Opérations',
            'color' => '#38b2ac',
            'parent_id' => $dg->id,
            'order' => 2,
        ]);

        $daf = Department::create([
            'name' => 'Direction Administrative et Financière',
            'code' => 'DAF',
            'description' => 'Direction Administrative et Financière',
            'color' => '#e3342f',
            'parent_id' => $dg->id,
            'order' => 3,
        ]);

        // Sous-départements de la Direction des Opérations
        $logistics = Department::create([
            'name' => 'Service Logistique',
            'code' => 'LOGISTIQUE',
            'description' => 'Responsable du Service Logistique',
            'color' => '#4299e1',
            'parent_id' => $dirOps->id,
            'order' => 1,
        ]);

        $coordination = Department::create([
            'name' => 'Coordination des Opérations',
            'code' => 'COORDINATION',
            'description' => 'Équipe Coordination des Opérations',
            'color' => '#48bb78',
            'parent_id' => $dirOps->id,
            'order' => 2,
        ]);

        $documentation = Department::create([
            'name' => 'Gestion de la Documentation',
            'code' => 'DOCUMENTATION',
            'description' => 'Équipe Gestion de la Documentation',
            'color' => '#ed8936',
            'parent_id' => $dirOps->id,
            'order' => 3,
        ]);

        $douane = Department::create([
            'name' => 'Équipe Douane',
            'code' => 'DOUANE',
            'description' => 'Équipe Suivi des Partenaires Logistiques et Douane',
            'color' => '#9f7aea',
            'parent_id' => $dirOps->id,
            'order' => 4,
        ]);

        // Sous-départements DAF
        $compta = Department::create([
            'name' => 'Comptabilité P2P & O2C',
            'code' => 'COMPTA',
            'description' => 'Responsable Comptabilité P2P & O2C',
            'color' => '#f56565',
            'parent_id' => $daf->id,
            'order' => 1,
        ]);

        $finance = Department::create([
            'name' => 'Finance, Audit & Qualité',
            'code' => 'FINANCE',
            'description' => 'Responsable Finance, Audit & Qualité',
            'color' => '#ed64a6',
            'parent_id' => $daf->id,
            'order' => 2,
        ]);

        $rh = Department::create([
            'name' => 'RH & Affaires Généraux',
            'code' => 'RH',
            'description' => 'Responsable des RH & Affaires Généraux',
            'color' => '#4fd1c5',
            'parent_id' => $daf->id,
            'order' => 3,
        ]);

        // === POSITIONS ET MEMBRES ===

        // Direction Générale
        $posDG = Position::create([
            'title' => 'Directrice Générale',
            'description' => 'Direction Générale du Groupe TLT',
            'department_id' => $dg->id,
            'level' => 1,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posDG->id,
            'name' => 'Mme Prisca',
            'status' => 'ACTIVE',
        ]);

        // Direction Commerciale
        $posDirCom = Position::create([
            'title' => 'Directeur Commercial',
            'description' => 'Direction Commerciale',
            'department_id' => $dirCom->id,
            'parent_position_id' => $posDG->id,
            'level' => 2,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posDirCom->id,
            'name' => 'Steffy',
            'status' => 'ACTIVE',
        ]);

        // Sales
        $posSales1 = Position::create([
            'title' => 'Sales 1',
            'department_id' => $dirCom->id,
            'parent_position_id' => $posDirCom->id,
            'level' => 3,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posSales1->id,
            'name' => 'Aina',
            'status' => 'ACTIVE',
        ]);

        $posSales2 = Position::create([
            'title' => 'Sales 2',
            'department_id' => $dirCom->id,
            'parent_position_id' => $posDirCom->id,
            'level' => 3,
            'order' => 2,
        ]);

        OrganizationMember::create([
            'position_id' => $posSales2->id,
            'name' => 'VACANT',
            'status' => 'VACANT',
        ]);

        $posSales3 = Position::create([
            'title' => 'Sales 3',
            'department_id' => $dirCom->id,
            'parent_position_id' => $posDirCom->id,
            'level' => 3,
            'order' => 3,
        ]);

        OrganizationMember::create([
            'position_id' => $posSales3->id,
            'name' => 'VACANT',
            'status' => 'VACANT',
        ]);

        // Direction des Opérations
        $posDirOps = Position::create([
            'title' => 'Directeur des Opérations',
            'description' => 'Direction des Opérations',
            'department_id' => $dirOps->id,
            'parent_position_id' => $posDG->id,
            'level' => 2,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posDirOps->id,
            'name' => 'VACANT',
            'status' => 'VACANT',
        ]);

        // Service Logistique
        $posLogistique = Position::create([
            'title' => 'Responsable Service Logistique',
            'description' => 'Achats des débours logistiques, recherche de camions, manutentionnaires, matériels',
            'responsibilities' => "Recherche de camion, manutentionnaires, matériels de déménagements, prestataires\nNégociations tarifs et délais de paiement\nComparaison\nS'assure de la fiabilité, conformité et qualité des prestataires\nPrend les mesures sécuritaires",
            'department_id' => $logistics->id,
            'parent_position_id' => $posDirOps->id,
            'level' => 3,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posLogistique->id,
            'name' => 'Justin',
            'status' => 'ACTIVE',
        ]);

        // Coordination
        $posCoordination = Position::create([
            'title' => 'Coordination des Opérations',
            'description' => 'Planification et exécution des opérations sur terrain',
            'responsibilities' => "Livraison, ramassage, manutention\nTransport\nChargement, Déchargement\nEmballage\nTout travail sur terrain",
            'department_id' => $coordination->id,
            'parent_position_id' => $posDirOps->id,
            'level' => 3,
            'order' => 2,
        ]);

        OrganizationMember::create([
            'position_id' => $posCoordination->id,
            'name' => 'Claude',
            'status' => 'ACTIVE',
        ]);

        // Documentation
        $posDocumentation = Position::create([
            'title' => 'Gestion de la Documentation',
            'description' => 'Gestion de toute la documentation requise pour les dossiers',
            'responsibilities' => "Ouverture de dossiers, OT, Contrats, BSC, MIDAC\nTaxation, Phyto, Autorisations, CO\nSuivi des Coûts\nDraft facture\nMontage du dossier physique opérationnelle avant transmission au service ARCHIVES",
            'department_id' => $documentation->id,
            'parent_position_id' => $posDirOps->id,
            'level' => 3,
            'order' => 3,
        ]);

        OrganizationMember::create([
            'position_id' => $posDocumentation->id,
            'name' => 'Tina Romio, Lala, Sandra',
            'status' => 'ACTIVE',
        ]);

        // Équipe Douane
        $posDouane = Position::create([
            'title' => 'Responsable Douane',
            'description' => 'Suivi des partenaires logistiques et formalités douanières',
            'responsibilities' => "Formalités douanières, Négociation douane\nRépertoire, gestion des Crédits douane\nSoumission, Suivi IM5\nEngagement annuel, Caution\nLitige Douane, Contentieux\nGarantir la conformité avec la règlementation\nVeille règlementaire Douane",
            'department_id' => $douane->id,
            'parent_position_id' => $posDirOps->id,
            'level' => 3,
            'order' => 4,
        ]);

        OrganizationMember::create([
            'position_id' => $posDouane->id,
            'name' => 'José/Mparany',
            'status' => 'ACTIVE',
        ]);

        // DAF
        $posDAF = Position::create([
            'title' => 'Directeur Administratif et Financier',
            'description' => 'Direction Administrative et Financière',
            'department_id' => $daf->id,
            'parent_position_id' => $posDG->id,
            'level' => 2,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posDAF->id,
            'name' => 'Haja',
            'status' => 'ACTIVE',
        ]);

        // Comptabilité
        $posComptabilite = Position::create([
            'title' => 'Responsable Comptabilité P2P & O2C',
            'description' => 'Gestion de la comptabilité fournisseurs et clients',
            'department_id' => $compta->id,
            'parent_position_id' => $posDAF->id,
            'level' => 3,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posComptabilite->id,
            'name' => 'Iarinah',
            'status' => 'ACTIVE',
        ]);

        // O2C
        $posO2C = Position::create([
            'title' => 'O2C - Encaissements & Comptabilité Clients',
            'description' => 'Réception OPT, Gestion des Provisions, Facturation, Suivi des Paiements',
            'responsibilities' => "Réception OPT\nGestion des Provisions\nFacturation\nSuivi des Paiements\nService Client concernant la facturation\nComptabilité générale Clients",
            'department_id' => $compta->id,
            'parent_position_id' => $posComptabilite->id,
            'level' => 4,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posO2C->id,
            'name' => 'Zara',
            'status' => 'ACTIVE',
        ]);

        // P2P
        $posP2P = Position::create([
            'title' => 'P2P - Décaissement & Comptabilité Fournisseurs',
            'description' => 'Réception DAN, Sélection FRNS, passation de commandes',
            'responsibilities' => "Réception DAN\nSélection FRNS\nPassation de commandes\nRelations FRNS\nCompta FRNS\nCaisse, Banque",
            'department_id' => $compta->id,
            'parent_position_id' => $posComptabilite->id,
            'level' => 4,
            'order' => 2,
        ]);

        OrganizationMember::create([
            'position_id' => $posP2P->id,
            'name' => 'Nary',
            'status' => 'ACTIVE',
        ]);

        // Finance
        $posFinance = Position::create([
            'title' => 'Responsable Finance, Audit & Qualité',
            'description' => 'Contrôleur de gestion, Prévisions Financières, Élaboration des Budgets',
            'responsibilities' => "Contrôleur de gestion\nPrévisions Financières\nÉlaboration des Budgets\nContrôle Budgétaire\nPlanification Stratégique\nAudit & Qualité",
            'department_id' => $finance->id,
            'parent_position_id' => $posDAF->id,
            'level' => 3,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posFinance->id,
            'name' => 'Zinà / Mparany',
            'status' => 'ACTIVE',
        ]);

        // RH
        $posRH = Position::create([
            'title' => 'Responsable RH & Affaires Généraux',
            'description' => 'Gestion des ressources humaines et affaires générales',
            'department_id' => $rh->id,
            'parent_position_id' => $posDAF->id,
            'level' => 3,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posRH->id,
            'name' => 'VACANT',
            'status' => 'VACANT',
        ]);

        // Informatique
        $posIT = Position::create([
            'title' => 'Informatique',
            'description' => 'Support technique et gestion des systèmes',
            'responsibilities' => "Support technique et assistance en cas de problèmes\nGestion des systèmes\nSécurités des données\nDéveloppement et programmation\nGestion technique des fournisseurs informatiques\nGestion des projets informatiques",
            'department_id' => $rh->id,
            'parent_position_id' => $posRH->id,
            'level' => 4,
            'order' => 1,
        ]);

        OrganizationMember::create([
            'position_id' => $posIT->id,
            'name' => 'Andry',
            'status' => 'ACTIVE',
        ]);

        // Gestion du Personnel
        $posGestionPersonnel = Position::create([
            'title' => 'Gestion du Personnel',
            'description' => 'Gestion Administrative et opérationnelles liées aux employés',
            'responsibilities' => "Paie, congé, embauche, débauches, contrats\nRetards, avertissements\nRelations avec les organismes CNAPS, ESIA",
            'department_id' => $rh->id,
            'parent_position_id' => $posRH->id,
            'level' => 4,
            'order' => 2,
        ]);

        OrganizationMember::create([
            'position_id' => $posGestionPersonnel->id,
            'name' => 'Zara',
            'status' => 'ACTIVE',
        ]);

        // Intendance / Patrimoine
        $posIntendance = Position::create([
            'title' => 'Intendance / Patrimoine',
            'description' => 'Entretien et maintenance des locaux, gestion du patrimoine',
            'responsibilities' => "Entretien et maintenance des locaux\nSécurité\nAménagements des espaces\nInventaires des actifs\nAcquisition, cession, recyclage\nTravaux de réparation rénovation\nConservation du patrimoine",
            'department_id' => $rh->id,
            'parent_position_id' => $posRH->id,
            'level' => 4,
            'order' => 3,
        ]);

        OrganizationMember::create([
            'position_id' => $posIntendance->id,
            'name' => 'VACANT',
            'status' => 'VACANT',
        ]);

        $this->command->info('Organigramme TLT créé avec succès !');
    }
}
