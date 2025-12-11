<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatrimoineController;
use App\Http\Controllers\DemandeFournitureController;
use App\Http\Controllers\OrganigrammeController;
use App\Http\Controllers\SSOController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\DemandeAbsenceController;
use App\Http\Controllers\DocumentEmployeController;
use App\Http\Controllers\GestionPersonnelController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ============================================
// Routes d'authentification centralisées
// ============================================
Route::prefix('auth')->name('auth.')->group(function () {
    // Pages de connexion et inscription
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout']); // Support GET aussi
    Route::get('/continue-logout', [AuthController::class, 'continueLogout'])->name('continue-logout');
    Route::post('/final-logout', [AuthController::class, 'finalLogout'])->name('final-logout');
});

// Route de connexion legacy - redirige vers la nouvelle route
Route::get('/login', function() {
    return redirect()->route('auth.login', ['site' => 'admin']);
})->name('login');

// ============================================
// Routes principales - TOUTES PROTÉGÉES
// ============================================

// Middleware pour protéger toutes les routes sauf auth
Route::middleware(['web', 'auth'])->group(function () {
    
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return redirect('/');
    });

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

// Organigramme SSO
Route::get('/organigramme', [OrganigrammeController::class, 'index'])->name('organigramme.index');
Route::get('/organigramme/roles-data', [OrganigrammeController::class, 'getRolesData'])->name('organigramme.roles-data');
Route::get('/organigramme/flow-data', [OrganigrammeController::class, 'getFlowData'])->name('organigramme.flow-data');

// Organigramme de la société TLT
Route::prefix('organigramme')->name('organigramme.')->group(function () {
    Route::get('/data', [OrganigrammeController::class, 'getData'])->name('data');
    Route::get('/interactive', [OrganigrammeController::class, 'interactive'])->name('interactive');
    
    // Départements
    Route::post('/departments', [OrganigrammeController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [OrganigrammeController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}', [OrganigrammeController::class, 'destroyDepartment'])->name('departments.destroy');
    
    // Positions
    Route::post('/positions', [OrganigrammeController::class, 'storePosition'])->name('positions.store');
    Route::get('/positions/{position}/view', [OrganigrammeController::class, 'viewPosition'])->name('positions.view');
    Route::put('/positions/{position}', [OrganigrammeController::class, 'updatePosition'])->name('positions.update');
    Route::delete('/positions/{position}', [OrganigrammeController::class, 'destroyPosition'])->name('positions.destroy');
    
    // Gestion des membres et statuts
    Route::get('/members', [\App\Http\Controllers\MemberStatusController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [\App\Http\Controllers\MemberStatusController::class, 'show'])->name('members.show');
    Route::get('/members/{member}/edit', [\App\Http\Controllers\MemberStatusController::class, 'edit'])->name('members.edit');
    Route::get('/members/{member}/leave-status', [OrganigrammeController::class, 'getMemberLeaveStatus'])->name('members.leave-status');
    Route::post('/members', [OrganigrammeController::class, 'storeMember'])->name('members.store');
    Route::put('/members/{member}', [\App\Http\Controllers\MemberStatusController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [OrganigrammeController::class, 'destroyMember'])->name('members.destroy');
    Route::get('/positions/{position}/assign', [\App\Http\Controllers\MemberStatusController::class, 'assignForm'])->name('positions.assign-form');
    Route::post('/positions/{position}/assign', [\App\Http\Controllers\MemberStatusController::class, 'assign'])->name('members.assign');
    Route::post('/members/{member}/demission', [\App\Http\Controllers\MemberStatusController::class, 'demission'])->name('members.demission');
    Route::post('/members/{member}/licenciement', [\App\Http\Controllers\MemberStatusController::class, 'licenciement'])->name('members.licenciement');
    Route::post('/members/{member}/retraite', [\App\Http\Controllers\MemberStatusController::class, 'retraite'])->name('members.retraite');
    Route::post('/members/{member}/reaffectation', [\App\Http\Controllers\MemberStatusController::class, 'requestReaffectation'])->name('members.reaffectation.request');
    Route::post('/reaffectation/{reaffectation}/approve', [\App\Http\Controllers\MemberStatusController::class, 'approveReaffectation'])->name('members.reaffectation.approve');
    Route::post('/reaffectation/{reaffectation}/reject', [\App\Http\Controllers\MemberStatusController::class, 'rejectReaffectation'])->name('members.reaffectation.reject');
    Route::get('/members-vacant', [\App\Http\Controllers\MemberStatusController::class, 'vacantPositions'])->name('members.vacant');
    Route::get('/members-history', [\App\Http\Controllers\MemberStatusController::class, 'statusHistory'])->name('members.history');
    
    // Mise à jour hiérarchie
    Route::post('/update-hierarchy', [OrganigrammeController::class, 'updateHierarchy'])->name('update-hierarchy');
});

// SSO Routes
Route::get('/sso/login', [SSOController::class, 'login'])->name('sso.login');
Route::post('/sso/authenticate', [SSOController::class, 'authenticate'])->name('sso.authenticate');
Route::get('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');
Route::post('/sso/revoke-token', [SSOController::class, 'revokeToken'])->name('sso.revoke-token');

// Admin Routes - Gestion des utilisateurs et rôles
Route::prefix('admin')->name('admin.')->group(function () {
    // Utilisateurs
    Route::resource('users', UserController::class);
    Route::post('users/{user}/revoke-tokens', [UserController::class, 'revokeTokens'])->name('users.revoke-tokens');
    
    // Rôles
    Route::resource('roles', RoleController::class);
});

// Gestion des Congés
Route::prefix('conges')->name('conges.')->group(function () {
    Route::get('/', [CongeController::class, 'index'])->name('index');
    Route::get('/create', [CongeController::class, 'create'])->name('create');
    Route::post('/', [CongeController::class, 'store'])->name('store');
    Route::get('/{conge}', [CongeController::class, 'show'])->name('show');
    Route::post('/{conge}/approve', [CongeController::class, 'approve'])->name('approve');
    Route::post('/{conge}/reject', [CongeController::class, 'reject'])->name('reject');
    Route::delete('/{conge}', [CongeController::class, 'destroy'])->name('destroy');
});

// Gestion des Absences
Route::prefix('absences')->name('absences.')->group(function () {
    Route::get('/', [DemandeAbsenceController::class, 'index'])->name('index');
    Route::get('/create', [DemandeAbsenceController::class, 'create'])->name('create');
    Route::post('/', [DemandeAbsenceController::class, 'store'])->name('store');
    Route::get('/{absence}', [DemandeAbsenceController::class, 'show'])->name('show');
    Route::post('/{absence}/approve', [DemandeAbsenceController::class, 'approve'])->name('approve');
    Route::post('/{absence}/reject', [DemandeAbsenceController::class, 'reject'])->name('reject');
});

// Gestion des Documents
Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/', [DocumentEmployeController::class, 'index'])->name('index');
    Route::get('/create', [DocumentEmployeController::class, 'create'])->name('create');
    Route::post('/', [DocumentEmployeController::class, 'store'])->name('store');
    Route::get('/{document}', [DocumentEmployeController::class, 'show'])->name('show');
    Route::get('/{document}/download', [DocumentEmployeController::class, 'download'])->name('download');
    Route::post('/request', [DocumentEmployeController::class, 'requestDocument'])->name('request');
    Route::post('/{document}/archive', [DocumentEmployeController::class, 'archive'])->name('archive');
    Route::delete('/{document}', [DocumentEmployeController::class, 'destroy'])->name('destroy');
});

// Gestion du Personnel (RH)
Route::prefix('personnel')->name('personnel.')->group(function () {
    Route::get('/', [GestionPersonnelController::class, 'index'])->name('index');
    Route::get('/{membre}', [GestionPersonnelController::class, 'show'])->name('show');
    Route::get('/{membre}/change-status', [GestionPersonnelController::class, 'changeStatusForm'])->name('change-status-form');
    Route::post('/{membre}/change-status', [GestionPersonnelController::class, 'changeStatus'])->name('change-status');
    Route::get('/{membre}/historique', [GestionPersonnelController::class, 'historique'])->name('historique');
});

}); // Fin du groupe middleware auth
