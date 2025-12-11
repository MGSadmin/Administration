<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatrimoineController;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;

// Endpoint de test
Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

// ============================================================================
// OAUTH2 / PASSPORT ROUTES (Serveur d'authentification central)
// ============================================================================

// Route pour récupérer l'utilisateur authentifié via OAuth2 (utilisée par tous les sites)
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json(
        $request->user()->load('roles', 'permissions')
    );
});

// Routes API d'authentification pour les sites clients
Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()->load('roles', 'permissions'),
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            'roles' => $request->user()->getRoleNames(),
        ]);
    });
    
    Route::get('/permissions', function (Request $request) {
        return response()->json([
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
        ]);
    });
    
    Route::get('/roles', function (Request $request) {
        return response()->json([
            'roles' => $request->user()->getRoleNames(),
        ]);
    });
});

// SSO Authentication API - Routes publiques (compatibilité Sanctum)
Route::prefix('auth')->group(function () {
    // Ces routes sont accessibles sans authentification pour les sites clients
    Route::post('/verify-token', [AuthController::class, 'verifyToken'])
        ->middleware('auth:sanctum');
    
    Route::get('/user-permissions/{siteCode}', [AuthController::class, 'getUserPermissions'])
        ->middleware('auth:sanctum');
    
    Route::post('/check-permission', [AuthController::class, 'checkPermission'])
        ->middleware('auth:sanctum');
    
    Route::post('/check-role', [AuthController::class, 'checkRole'])
        ->middleware('auth:sanctum');
    
    Route::get('/me', [AuthController::class, 'me'])
        ->middleware('auth:sanctum');
    
    Route::get('/accessible-sites', [AuthController::class, 'getAccessibleSites'])
        ->middleware('auth:sanctum');
    
    Route::post('/refresh-token', [AuthController::class, 'refreshToken'])
        ->middleware('auth:sanctum');
    
    Route::get('/tokens', [AuthController::class, 'listTokens'])
        ->middleware('auth:sanctum');
});

// API centralisée des notifications - accessible par tous les sites
Route::prefix('notifications')->middleware('auth:api')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\NotificationApiController::class, 'index']);
    Route::get('/unread-count', [App\Http\Controllers\Api\NotificationApiController::class, 'unreadCount']);
    Route::post('/send', [App\Http\Controllers\Api\NotificationApiController::class, 'store']);
    Route::patch('/{id}/mark-as-read', [App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead']);
    Route::post('/mark-all-as-read', [App\Http\Controllers\Api\NotificationApiController::class, 'markAllAsRead']);
    Route::delete('/{id}', [App\Http\Controllers\Api\NotificationApiController::class, 'destroy']);
});

// Patrimoines
Route::apiResource('patrimoines', PatrimoineController::class)->names([
    'index' => 'api.patrimoines.index',
    'show' => 'api.patrimoines.show',
    'store' => 'api.patrimoines.store',
    'update' => 'api.patrimoines.update',
    'destroy' => 'api.patrimoines.destroy',
]);

// Actions spéciales sur patrimoines
Route::post('patrimoines/{patrimoine}/photos', [PatrimoineController::class, 'uploadPhoto'])->name('api.patrimoines.uploadPhoto');
Route::get('patrimoines/{patrimoine}/photos', [PatrimoineController::class, 'getPhotos'])->name('api.patrimoines.getPhotos');
Route::post('patrimoines/{patrimoine}/attribuer', [PatrimoineController::class, 'attribuer'])->name('api.patrimoines.attribuer');
Route::post('patrimoines/{patrimoine}/liberer', [PatrimoineController::class, 'liberer'])->name('api.patrimoines.liberer');
Route::post('patrimoines/{patrimoine}/maintenance', [PatrimoineController::class, 'mettreEnMaintenance'])->name('api.patrimoines.mettreEnMaintenance');
Route::post('patrimoines/{patrimoine}/reformer', [PatrimoineController::class, 'reformer'])->name('api.patrimoines.reformer');
