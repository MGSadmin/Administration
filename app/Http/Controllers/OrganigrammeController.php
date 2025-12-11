<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\OrganizationMember;
use App\Models\Conge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class OrganigrammeController extends Controller
{
    public function index()
    {
        // Récupérer les statistiques pour l'organigramme
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'sites' => $this->getSitesStats(),
            'rolesList' => Role::withCount('users', 'permissions')->get(),
            'recentUsers' => User::with('roles')->latest()->take(5)->get(),
        ];

        return view('organigramme', compact('stats'));
    }

    public function getData()
    {
        $positions = Position::with(['department', 'parentPosition'])->get();
        
        $data = $positions->map(function ($position) {
            // Récupérer uniquement le membre ACTIF ou VACANT (pas les licenciés/démissionnaires)
            $member = OrganizationMember::where('position_id', $position->id)
                ->whereIn('status', [
                    OrganizationMember::STATUS_ACTIVE,
                    OrganizationMember::STATUS_VACANT,
                    OrganizationMember::STATUS_INTERIM
                ])
                ->with('user')
                ->first();
            
            $onLeave = false;
            
            // Vérifier si le membre est en congé
            if ($member && $member->status === OrganizationMember::STATUS_ACTIVE) {
                $currentLeave = Conge::where('organization_member_id', $member->id)
                    ->where('statut', Conge::STATUT_APPROUVE)
                    ->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now())
                    ->first();
                
                $onLeave = $currentLeave !== null;
            }
            
            return [
                'id' => $position->id,
                'name' => $member ? $member->name : 'VACANT',
                'title' => $position->title,
                'department' => $position->department->name,
                'departmentColor' => $position->department->color,
                'status' => $member ? $member->status : 'VACANT',
                'parentId' => $position->parent_position_id,
                'level' => $position->level,
                'description' => $position->description,
                'responsibilities' => $position->responsibilities,
                'email' => $member ? $member->email : null,
                'phone' => $member ? $member->phone : null,
                'photo' => $member ? $member->photo : null,
                'onLeave' => $onLeave,
            ];
        });

        return response()->json($data);
    }
    
    // Récupérer la vue interactive
    public function interactive()
    {
        return view('organigramme.interactive');
    }
    
    // Vérifier le statut de congé d'un membre
    public function getMemberLeaveStatus(OrganizationMember $member)
    {
        $currentLeave = Conge::where('organization_member_id', $member->id)
            ->where('statut', Conge::STATUT_APPROUVE)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->first();
        
        if ($currentLeave) {
            return response()->json([
                'isOnLeave' => true,
                'type' => $currentLeave->type,
                'date_debut' => $currentLeave->date_debut->format('d/m/Y'),
                'date_fin' => $currentLeave->date_fin->format('d/m/Y'),
                'nb_jours' => $currentLeave->nb_jours,
                'motif' => $currentLeave->motif,
            ]);
        }
        
        return response()->json(['isOnLeave' => false]);
    }

    // CRUD pour les départements
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'color' => 'nullable|string',
        ]);

        $department = Department::create($validated);

        return response()->json(['success' => true, 'department' => $department]);
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'color' => 'nullable|string',
        ]);

        $department->update($validated);

        return response()->json(['success' => true, 'department' => $department]);
    }

    public function destroyDepartment(Department $department)
    {
        $department->delete();
        return response()->json(['success' => true]);
    }

    // CRUD pour les positions
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'parent_position_id' => 'nullable|exists:positions,id',
            'level' => 'nullable|integer',
        ]);

        $position = Position::create($validated);

        return response()->json(['success' => true, 'position' => $position->load('department', 'member')]);
    }

    public function updatePosition(Request $request, Position $position)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'parent_position_id' => 'nullable|exists:positions,id',
            'level' => 'nullable|integer',
        ]);

        $position->update($validated);

        return response()->json(['success' => true, 'position' => $position->load('department', 'member')]);
    }

    public function destroyPosition(Position $position)
    {
        $position->delete();
        return response()->json(['success' => true]);
    }
    
    // Voir les détails d'une position
    public function viewPosition(Position $position)
    {
        $position->load(['department', 'member.user.roles', 'parentPosition']);
        
        return response()->json([
            'success' => true,
            'position' => $position
        ]);
    }

    // CRUD pour les membres
    public function storeMember(Request $request)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required_without:user_id|string|max:255',
            'status' => 'required|in:ACTIVE,VACANT,INTERIM',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'photo' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $member = OrganizationMember::create($validated);

        return response()->json(['success' => true, 'member' => $member->load('position', 'user')]);
    }

    public function updateMember(Request $request, OrganizationMember $member)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required_without:user_id|string|max:255',
            'status' => 'required|in:ACTIVE,VACANT,INTERIM',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'photo' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $member->update($validated);

        return response()->json(['success' => true, 'member' => $member->load('position', 'user')]);
    }

    public function destroyMember(OrganizationMember $member)
    {
        $member->delete();
        return response()->json(['success' => true]);
    }

    // Mise à jour de la hiérarchie (drag & drop)
    public function updateHierarchy(Request $request)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'parent_position_id' => 'nullable|exists:positions,id',
        ]);

        $position = Position::find($validated['position_id']);
        $position->parent_position_id = $validated['parent_position_id'];
        $position->save();

        return response()->json(['success' => true, 'position' => $position]);
    }

    private function getSitesStats()
    {
        return [
            [
                'name' => 'Administration',
                'domain' => 'administration.mgs.mg',
                'color' => '#667eea',
                'role' => 'Serveur Central SSO',
                'users' => User::count(),
                'permissions' => Permission::where('name', 'like', 'admin.%')->count(),
            ],
            [
                'name' => 'Commercial',
                'domain' => 'commercial.mgs.mg',
                'color' => '#f6993f',
                'role' => 'Client SSO',
                'users' => User::whereHas('permissions', function($q) {
                    $q->where('name', 'like', 'commercial.%');
                })->count(),
                'permissions' => Permission::where('name', 'like', 'commercial.%')->count(),
            ],
            [
                'name' => 'Gestion Dossier',
                'domain' => 'debours.mgs.mg',
                'color' => '#38b2ac',
                'role' => 'Client SSO',
                'users' => User::whereHas('permissions', function($q) {
                    $q->where('name', 'like', 'debours.%');
                })->count(),
                'permissions' => Permission::where('name', 'like', 'debours.%')->count(),
            ],
        ];
    }

    public function getRolesData()
    {
        $roles = Role::with(['users', 'permissions'])->get();
        
        return response()->json([
            'roles' => $roles->map(function($role) {
                return [
                    'name' => $role->name,
                    'users_count' => $role->users->count(),
                    'permissions' => $role->permissions->pluck('name'),
                ];
            }),
        ]);
    }

    public function getFlowData()
    {
        return response()->json([
            'authentication_flow' => [
                'steps' => [
                    '1. Utilisateur visite commercial.mgs.mg',
                    '2. Redirection vers administration.mgs.mg/sso/login',
                    '3. Saisie des identifiants',
                    '4. Vérification des permissions',
                    '5. Génération du token',
                    '6. Redirection avec token',
                    '7. Stockage en session',
                ],
            ],
            'verification_flow' => [
                'steps' => [
                    '1. Navigation sur le site client',
                    '2. Middleware CentralAuth vérifie le token',
                    '3. Validation auprès du serveur central',
                    '4. Vérification des permissions spécifiques',
                    '5. Autorisation d\'accès',
                ],
            ],
        ]);
    }
}
