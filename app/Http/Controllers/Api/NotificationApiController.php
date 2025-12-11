<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * API centralisée pour les notifications
 * Permet aux sites commercial et gestion-dossier d'accéder aux notifications
 * stockées dans la base administration
 */
class NotificationApiController extends Controller
{
    /**
     * Récupérer les notifications d'un utilisateur
     * 
     * GET /api/notifications?user_id=123
     */
    public function index(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            $unreadOnly = $request->boolean('unread_only', false);
            $limit = $request->input('limit', 50);
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id requis'
                ], 400);
            }
            
            $query = DB::connection('mysql')
                ->table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit);
            
            if ($unreadOnly) {
                $query->whereNull('read_at');
            }
            
            $notifications = $query->get();
            
            // Décoder les données JSON
            $notifications = $notifications->map(function ($notification) {
                $notification->data = json_decode($notification->data, true);
                return $notification;
            });
            
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'count' => $notifications->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur API notifications index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notifications'
            ], 500);
        }
    }
    
    /**
     * Compter les notifications non lues
     * 
     * GET /api/notifications/unread-count?user_id=123
     */
    public function unreadCount(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id requis'
                ], 400);
            }
            
            $count = DB::connection('mysql')
                ->table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur API notifications unread count', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du comptage des notifications'
            ], 500);
        }
    }
    
    /**
     * Créer une nouvelle notification
     * 
     * POST /api/notifications
     * {
     *   "user_id": 123,
     *   "type": "App\\Notifications\\NewDossierNotification",
     *   "data": {...}
     * }
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'type' => 'required|string',
                'data' => 'required|array'
            ]);
            
            $notificationId = Str::uuid()->toString();
            
            DB::connection('mysql')->table('notifications')->insert([
                'id' => $notificationId,
                'type' => $request->type,
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $request->user_id,
                'data' => json_encode($request->data),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('Notification créée via API', [
                'notification_id' => $notificationId,
                'user_id' => $request->user_id,
                'type' => $request->type
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notification créée avec succès',
                'notification_id' => $notificationId
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Erreur API notifications store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la notification'
            ], 500);
        }
    }
    
    /**
     * Marquer une notification comme lue
     * 
     * PATCH /api/notifications/{id}/mark-as-read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $updated = DB::connection('mysql')
                ->table('notifications')
                ->where('id', $id)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
            
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marquée comme lue'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée ou déjà lue'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur API notifications mark as read', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }
    
    /**
     * Marquer toutes les notifications comme lues
     * 
     * POST /api/notifications/mark-all-as-read
     * {
     *   "user_id": 123
     * }
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $userId = $request->input('user_id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'user_id requis'
                ], 400);
            }
            
            $updated = DB::connection('mysql')
                ->table('notifications')
                ->where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications marquées comme lues',
                'count' => $updated
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur API notifications mark all as read', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }
    
    /**
     * Supprimer une notification
     * 
     * DELETE /api/notifications/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $deleted = DB::connection('mysql')
                ->table('notifications')
                ->where('id', $id)
                ->delete();
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification supprimée'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erreur API notifications destroy', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }
}
