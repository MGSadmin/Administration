<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Vérifie la validité d'un token
     */
    public function verifyToken(Request $request)
    {
        $user = $request->user('sanctum');
        
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Token invalide'], 401);
        }
        
        return response()->json([
            'valid' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'roles' => $user->roles->pluck('name'),
            'all_permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /**
     * Récupère les permissions d'un utilisateur pour un site spécifique
     */
    public function getUserPermissions(Request $request, $siteCode)
    {
        $user = $request->user('sanctum');
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
        
        $site = Site::where('code', $siteCode)->where('is_active', true)->first();
        
        if (!$site) {
            return response()->json(['error' => 'Site non trouvé'], 404);
        }
        
        // Filtrer les permissions par site
        $permissions = $user->getAllPermissions()
            ->filter(function($permission) use ($siteCode) {
                return str_starts_with($permission->name, $siteCode . '.');
            })
            ->pluck('name')
            ->values();
        
        return response()->json([
            'site' => $site->name,
            'site_code' => $site->code,
            'permissions' => $permissions,
            'has_access' => $permissions->isNotEmpty(),
        ]);
    }
    
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    public function checkPermission(Request $request)
    {
        $request->validate([
            'permission' => 'required|string'
        ]);
        
        $user = $request->user('sanctum');
        $permission = $request->input('permission');
        
        if (!$user) {
            return response()->json(['has_permission' => false, 'message' => 'Non autorisé'], 401);
        }
        
        $hasPermission = $user->hasPermissionTo($permission);
        
        return response()->json([
            'has_permission' => $hasPermission,
            'permission' => $permission,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public function checkRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string'
        ]);
        
        $user = $request->user('sanctum');
        $role = $request->input('role');
        
        if (!$user) {
            return response()->json(['has_role' => false, 'message' => 'Non autorisé'], 401);
        }
        
        $hasRole = $user->hasRole($role);
        
        return response()->json([
            'has_role' => $hasRole,
            'role' => $role,
            'user_roles' => $user->roles->pluck('name'),
        ]);
    }

    /**
     * Récupère les informations complètes de l'utilisateur authentifié
     */
    public function me(Request $request)
    {
        $user = $request->user('sanctum');
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'roles' => $user->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => $role->permissions->count(),
                ];
            }),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'direct_permissions' => $user->permissions->pluck('name'),
        ]);
    }

    /**
     * Liste les sites accessibles pour l'utilisateur
     */
    public function getAccessibleSites(Request $request)
    {
        $user = $request->user('sanctum');
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
        
        $sites = Site::where('is_active', true)->get();
        $accessibleSites = [];
        
        foreach ($sites as $site) {
            $permissions = $user->getAllPermissions()
                ->filter(fn($p) => str_starts_with($p->name, $site->code . '.'))
                ->pluck('name');
            
            if ($permissions->isNotEmpty()) {
                $accessibleSites[] = [
                    'id' => $site->id,
                    'name' => $site->name,
                    'domain' => $site->domain,
                    'code' => $site->code,
                    'permissions' => $permissions,
                ];
            }
        }
        
        return response()->json([
            'sites' => $accessibleSites,
            'total' => count($accessibleSites),
        ]);
    }

    /**
     * Rafraîchit le token (crée un nouveau token et révoque l'ancien)
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user('sanctum');
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
        
        // Récupérer le token actuel
        $currentToken = $request->user('sanctum')->currentAccessToken();
        
        // Créer un nouveau token
        $newToken = $user->createToken('refreshed-token', ['*'], now()->addDays(7));
        
        // Révoquer l'ancien token
        if ($currentToken) {
            $currentToken->delete();
        }
        
        return response()->json([
            'token' => $newToken->plainTextToken,
            'expires_at' => $newToken->accessToken->expires_at,
        ]);
    }

    /**
     * Liste tous les tokens actifs de l'utilisateur
     */
    public function listTokens(Request $request)
    {
        $user = $request->user('sanctum');
        
        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }
        
        $tokens = $user->tokens()->get()->map(function($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => $token->abilities,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
                'expires_at' => $token->expires_at,
            ];
        });
        
        return response()->json([
            'tokens' => $tokens,
            'total' => $tokens->count(),
        ]);
    }
}
