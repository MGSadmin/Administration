<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatrimoineController;

Route::middleware('auth:sanctum')->group(function () {
    // Patrimoines
    Route::apiResource('patrimoines', PatrimoineController::class);
    
    // Actions spéciales sur patrimoines
    Route::post('patrimoines/{patrimoine}/photos', [PatrimoineController::class, 'uploadPhoto']);
    Route::get('patrimoines/{patrimoine}/photos', [PatrimoineController::class, 'getPhotos']);
    Route::post('patrimoines/{patrimoine}/attribuer', [PatrimoineController::class, 'attribuer']);
    Route::post('patrimoines/{patrimoine}/liberer', [PatrimoineController::class, 'liberer']);
    Route::post('patrimoines/{patrimoine}/maintenance', [PatrimoineController::class, 'mettreEnMaintenance']);
    Route::post('patrimoines/{patrimoine}/reformer', [PatrimoineController::class, 'reformer']);
});

// Routes publiques pour authentification
Route::post('login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::middleware('auth:sanctum')->post('logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});
