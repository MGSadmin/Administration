<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatrimoineController;
use App\Http\Controllers\DemandeFournitureController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    
    // Gestion des rôles
    Route::resource('roles', RoleController::class);
    
    // Documentation des rôles et permissions
    Route::get('/roles-documentation', function() {
        return view('roles.documentation');
    })->name('roles.documentation');
    
    // Gestion des notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew'])->name('notifications.check-new');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Gestion des patrimoines
    Route::resource('patrimoines', PatrimoineController::class);
    Route::post('/patrimoines/{patrimoine}/attribuer', [PatrimoineController::class, 'attribuer'])->name('patrimoines.attribuer');
    Route::post('/patrimoines/{patrimoine}/liberer', [PatrimoineController::class, 'liberer'])->name('patrimoines.liberer');
    Route::get('/patrimoines-statistiques', [PatrimoineController::class, 'statistiques'])->name('patrimoines.statistiques');
    
    // Gestion des demandes de fourniture
    Route::resource('demandes-fourniture', DemandeFournitureController::class)->parameters([
        'demandes-fourniture' => 'demandeFourniture'
    ]);
    Route::post('/demandes-fourniture/{demandeFourniture}/valider', [DemandeFournitureController::class, 'valider'])->name('demandes-fourniture.valider');
    Route::post('/demandes-fourniture/{demandeFourniture}/rejeter', [DemandeFournitureController::class, 'rejeter'])->name('demandes-fourniture.rejeter');
    Route::post('/demandes-fourniture/{demandeFourniture}/commander', [DemandeFournitureController::class, 'commander'])->name('demandes-fourniture.commander');
    Route::post('/demandes-fourniture/{demandeFourniture}/marquer-recue', [DemandeFournitureController::class, 'marquerRecue'])->name('demandes-fourniture.marquer-recue');
    Route::post('/demandes-fourniture/{demandeFourniture}/livrer', [DemandeFournitureController::class, 'livrer'])->name('demandes-fourniture.livrer');
    Route::get('/demandes-fourniture-statistiques', [DemandeFournitureController::class, 'statistiques'])->name('demandes-fourniture.statistiques');
});

require __DIR__.'/auth.php';
