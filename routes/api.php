<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatrimoineController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Routes publiques pour authentification
Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $data['email'])->first();

    if (! $user || ! Hash::check($data['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});

// Endpoint de test
Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});

Route::middleware('auth:sanctum')->group(function () {
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
});
