<?php

namespace App\Http\Controllers;

use App\Models\OrganizationMember;
use App\Models\Position;
use App\Models\User;
use App\Models\HistoriqueStatutMembre;
use App\Models\ReaffectationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberStatusController extends Controller
{
    /**
     * Afficher la liste des membres avec leur statut
     */
    public function index(Request $request)
    {
        $query = OrganizationMember::with(['position.department', 'user', 'historiqueStatuts']);

        // Filtres
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('department') && $request->department != '') {
            $query->whereHas('position', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(20);
        $vacantPositions = OrganizationMember::getVacantPositions();

        return view('organigramme.members.index', compact('members', 'vacantPositions'));
    }

    /**
     * Afficher le détail d'un membre avec historique
     */
    public function show(OrganizationMember $member)
    {
        $member->load([
            'position.department', 
            'user', 
            'historiqueStatuts.user',
            'conges',
            'demandesAbsence'
        ]);

        return view('organigramme.members.show', compact('member'));
    }

    /**
     * Afficher le formulaire d'édition d'un membre
     */
    public function edit(OrganizationMember $member)
    {
        $member->load(['position.department', 'user']);
        $positions = Position::with('department')->get();
        
        return view('organigramme.members.edit', compact('member', 'positions'));
    }

    /**
     * Mettre à jour les informations d'un membre
     */
    public function update(Request $request, OrganizationMember $member)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'linked_user_id' => 'nullable|exists:users,id',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:ACTIF,VACANT,INTERIM',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'keep_current_info' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();
            
            // Déterminer quel champ utilisateur utiliser (priorité à linked_user_id)
            $userId = $request->filled('linked_user_id') ? $request->linked_user_id : $request->user_id;
            
            // Vérifier si le poste change
            $positionChanged = $member->position_id != $request->position_id;
            
            if ($positionChanged) {
                // Vérifier s'il y a déjà un membre actif sur ce nouveau poste
                $existingMember = OrganizationMember::where('position_id', $request->position_id)
                    ->whereIn('status', ['ACTIF', 'INTERIM'])
                    ->where('id', '!=', $member->id)
                    ->first();
                
                if ($existingMember) {
                    return back()->withErrors(['position_id' => 'Ce poste est déjà occupé par ' . $existingMember->name])->withInput();
                }
            }
            
            // Si un utilisateur est assigné et qu'on ne garde pas les infos actuelles
            if ($userId && !$request->keep_current_info) {
                $user = User::findOrFail($userId);
                
                // Vérifier que l'utilisateur n'a pas déjà un poste actif
                $existingAssignment = OrganizationMember::where('user_id', $user->id)
                    ->where('status', OrganizationMember::STATUS_ACTIVE)
                    ->where('id', '!=', $member->id)
                    ->first();
                
                if ($existingAssignment) {
                    return back()->withErrors(['linked_user_id' => 'Cet utilisateur a déjà un poste actif.'])->withInput();
                }
                
                // Mettre à jour avec les infos de l'utilisateur
                $member->update([
                    'position_id' => $request->position_id,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->telephone,
                    'status' => $request->status,
                    'start_date' => $request->start_date ?? $member->start_date,
                ]);
                
                // Enregistrer le changement dans l'historique si changement de poste
                if ($positionChanged) {
                    HistoriqueStatutMembre::create([
                        'organization_member_id' => $member->id,
                        'ancien_statut' => $member->status,
                        'nouveau_statut' => $request->status,
                        'motif' => 'mutation',
                        'commentaire' => "Changement de poste vers " . $member->position->title,
                        'user_id' => auth()->id(),
                        'date_effectif' => now(),
                    ]);
                }
            } 
            // Si l'utilisateur est désassigné (user_id vide)
            elseif (!$userId && $request->status === 'VACANT') {
                // Marquer le poste comme vacant
                $member->update([
                    'position_id' => $request->position_id,
                    'user_id' => null,
                    'name' => 'VACANT',
                    'email' => null,
                    'phone' => null,
                    'status' => OrganizationMember::STATUS_VACANT,
                ]);
            }
            // Sinon mise à jour manuelle des informations (aucun utilisateur lié)
            else {
                $member->update([
                    'position_id' => $request->position_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'status' => $request->status,
                    'start_date' => $request->start_date,
                ]);
                
                // Enregistrer le changement dans l'historique si changement de poste
                if ($positionChanged) {
                    HistoriqueStatutMembre::create([
                        'organization_member_id' => $member->id,
                        'ancien_statut' => $member->status,
                        'nouveau_statut' => $request->status,
                        'motif' => 'mutation',
                        'commentaire' => "Changement de poste vers " . $member->position->title,
                        'user_id' => auth()->id(),
                        'date_effectif' => now(),
                    ]);
                }
            }
            
            DB::commit();

            return redirect()->route('organigramme.members.show', $member)
                ->with('success', 'Les informations du membre ont été mises à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Formulaire d'affectation d'un utilisateur à un poste
     */
    public function assignForm(Position $position)
    {
        $users = User::where('is_active', true)
            ->whereDoesntHave('organizationMember', function($q) {
                $q->where('status', OrganizationMember::STATUS_ACTIVE);
            })
            ->get();

        return view('organigramme.members.assign', compact('position', 'users'));
    }

    /**
     * Affecter un utilisateur à un poste
     */
    public function assign(Request $request, Position $position)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($request->user_id);

        // Vérifier si l'utilisateur a déjà un poste actif
        $existingAssignment = OrganizationMember::getActiveAssignmentForUser($user);
        if ($existingAssignment) {
            return back()->withErrors(['user_id' => 'Cet utilisateur a déjà un poste actif.']);
        }

        try {
            DB::beginTransaction();

            // Créer ou mettre à jour le membre de l'organisation
            $member = OrganizationMember::where('position_id', $position->id)->first();

            if ($member && $member->status === OrganizationMember::STATUS_VACANT) {
                $member->assignUser($user, $request->commentaire, auth()->id());
            } else {
                $member = OrganizationMember::create([
                    'position_id' => $position->id,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->telephone,
                    'status' => OrganizationMember::STATUS_ACTIVE,
                    'start_date' => now(),
                ]);

                HistoriqueStatutMembre::create([
                    'organization_member_id' => $member->id,
                    'ancien_statut' => 'NOUVEAU',
                    'nouveau_statut' => OrganizationMember::STATUS_ACTIVE,
                    'motif' => HistoriqueStatutMembre::MOTIF_EMBAUCHE,
                    'commentaire' => $request->commentaire,
                    'user_id' => auth()->id(),
                    'date_effectif' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('organigramme.interactive')
                ->with('success', "Utilisateur {$user->name} affecté au poste {$position->title} avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'affectation: ' . $e->getMessage()]);
        }
    }

    /**
     * Marquer un membre comme démissionnaire
     */
    public function demission(Request $request, OrganizationMember $member)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:500',
            'date_effectif' => 'nullable|date',
        ]);

        try {
            $member->markAsDemission($request->commentaire, auth()->id());

            return back()->with('success', 'Le membre a été marqué comme démissionnaire. Le poste est maintenant vacant.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Marquer un membre comme licencié
     */
    public function licenciement(Request $request, OrganizationMember $member)
    {
        $request->validate([
            'commentaire' => 'required|string|max:500',
        ]);

        try {
            $member->markAsLicencie($request->commentaire, auth()->id());

            return back()->with('success', 'Le membre a été marqué comme licencié. Le poste est maintenant vacant.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Marquer un membre comme retraité
     */
    public function retraite(Request $request, OrganizationMember $member)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:500',
        ]);

        try {
            $member->markAsRetraite($request->commentaire, auth()->id());

            return back()->with('success', 'Le membre a été marqué comme retraité. Le poste est maintenant vacant.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Demander une réaffectation
     */
    public function requestReaffectation(Request $request, OrganizationMember $member)
    {
        $request->validate([
            'new_position_id' => 'required|exists:positions,id',
            'motif' => 'required|string|max:500',
            'date_souhaite' => 'nullable|date',
        ]);

        try {
            $reaffectation = ReaffectationRequest::create([
                'organization_member_id' => $member->id,
                'current_position_id' => $member->position_id,
                'new_position_id' => $request->new_position_id,
                'requested_by' => auth()->id(),
                'motif' => $request->motif,
                'date_souhaite' => $request->date_souhaite,
                'status' => ReaffectationRequest::STATUS_PENDING,
            ]);

            return back()->with('success', 'Demande de réaffectation créée avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Approuver une demande de réaffectation
     */
    public function approveReaffectation(Request $request, ReaffectationRequest $reaffectation)
    {
        $request->validate([
            'commentaire' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();
            $reaffectation->approve(auth()->user(), $request->commentaire);
            DB::commit();

            return back()->with('success', 'Demande de réaffectation approuvée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Rejeter une demande de réaffectation
     */
    public function rejectReaffectation(Request $request, ReaffectationRequest $reaffectation)
    {
        $request->validate([
            'commentaire' => 'required|string|max:500',
        ]);

        try {
            $reaffectation->reject(auth()->user(), $request->commentaire);

            return back()->with('success', 'Demande de réaffectation rejetée.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Afficher les postes vacants
     */
    public function vacantPositions()
    {
        $vacantPositions = OrganizationMember::with(['position.department'])
            ->where('status', OrganizationMember::STATUS_VACANT)
            ->orWhereNull('user_id')
            ->get();

        $availableUsers = User::where('is_active', true)
            ->whereDoesntHave('organizationMember', function($q) {
                $q->where('status', OrganizationMember::STATUS_ACTIVE);
            })
            ->get();

        return view('organigramme.members.vacant', compact('vacantPositions', 'availableUsers'));
    }

    /**
     * Historique complet des changements de statut
     */
    public function statusHistory()
    {
        $historique = HistoriqueStatutMembre::with(['organizationMember.position', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('organigramme.members.history', compact('historique'));
    }
}
