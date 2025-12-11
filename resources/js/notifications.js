/**
 * ============================================================================
 * SYSTÈME DE NOTIFICATIONS TEMPS RÉEL
 * ============================================================================
 * 
 * Ce fichier gère l'écoute et l'affichage des notifications en temps réel
 * via Laravel Echo et Pusher/Soketi
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Récupérer l'ID de l'utilisateur connecté depuis la meta tag
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    
    if (!userId || !window.Echo) {
        console.log('Notifications temps réel non disponibles (utilisateur non connecté ou Echo non configuré)');
        return;
    }

    console.log(`Écoute des notifications pour l'utilisateur ${userId}`);

    /**
     * Écouter les notifications privées de l'utilisateur
     */
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            console.log('Notification reçue:', notification);
            
            // Afficher la notification
            showNotificationToast(notification);
            
            // Mettre à jour le badge de compteur
            updateNotificationBadge();
            
            // Ajouter à la liste des notifications (si elle existe)
            addToNotificationList(notification);
            
            // Jouer un son (optionnel)
            playNotificationSound();
        });

    /**
     * Écouter les canaux publics (annonces générales, etc.)
     */
    window.Echo.channel('annonces')
        .listen('AnnoncePubliee', (e) => {
            console.log('Annonce publique reçue:', e);
            showNotificationToast({
                titre: 'Nouvelle annonce',
                message: e.annonce.titre,
                type: 'info',
                url: `/annonces/${e.annonce.id}`
            });
        });

    /**
     * Charger le compteur initial de notifications non lues
     */
    updateNotificationBadge();
});

/**
 * Afficher une notification toast
 */
function showNotificationToast(notification) {
    const { titre, message, type, url } = notification;
    
    // Si vous utilisez une bibliothèque de notifications (Toastr, SweetAlert, etc.)
    // Exemple avec une notification HTML simple
    
    const toast = document.createElement('div');
    toast.className = `notification-toast notification-${type || 'info'}`;
    toast.innerHTML = `
        <div class="notification-content">
            <strong>${titre || 'Notification'}</strong>
            <p>${message}</p>
            ${url ? `<a href="${url}" class="notification-link">Voir les détails</a>` : ''}
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    // Ajouter au DOM
    let container = document.querySelector('.notification-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(toast);
    
    // Auto-fermeture après 5 secondes
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
    
    // Exemple avec Toastr (si installé)
    // if (typeof toastr !== 'undefined') {
    //     toastr[type || 'info'](message, titre, {
    //         onclick: url ? () => window.location.href = url : null
    //     });
    // }
}

/**
 * Mettre à jour le badge de compteur de notifications non lues
 */
function updateNotificationBadge() {
    fetch('/api/notifications/unread-count', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('#notification-count');
        if (badge) {
            badge.textContent = data.count;
            badge.style.display = data.count > 0 ? 'inline-block' : 'none';
        }
        
        // Mettre à jour l'icône (ajouter/retirer une classe)
        const bellIcon = document.querySelector('.notification-bell');
        if (bellIcon) {
            if (data.count > 0) {
                bellIcon.classList.add('has-notifications');
            } else {
                bellIcon.classList.remove('has-notifications');
            }
        }
    })
    .catch(error => {
        console.error('Erreur lors de la récupération du compteur:', error);
    });
}

/**
 * Ajouter la notification à la liste déroulante (si elle existe)
 */
function addToNotificationList(notification) {
    const list = document.querySelector('.notification-dropdown-list');
    if (!list) return;
    
    const item = document.createElement('div');
    item.className = 'notification-item unread';
    item.innerHTML = `
        <div class="notification-icon">
            <i class="fas fa-bell"></i>
        </div>
        <div class="notification-body">
            <strong>${notification.titre || 'Notification'}</strong>
            <p>${notification.message}</p>
            <small class="text-muted">À l'instant</small>
        </div>
        ${notification.url ? `<a href="${notification.url}" class="notification-action">Voir</a>` : ''}
    `;
    
    // Ajouter en première position
    list.insertBefore(item, list.firstChild);
    
    // Limiter à 10 notifications dans la liste
    if (list.children.length > 10) {
        list.removeChild(list.lastChild);
    }
}

/**
 * Jouer un son de notification (optionnel)
 */
function playNotificationSound() {
    // Vérifier si les sons sont activés dans les préférences utilisateur
    const soundEnabled = localStorage.getItem('notifications_sound') !== 'false';
    
    if (soundEnabled) {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.5;
        audio.play().catch(e => {
            // Ignorer les erreurs (autoplay peut être bloqué par le navigateur)
            console.log('Son de notification non joué:', e.message);
        });
    }
}

/**
 * Marquer une notification comme lue
 */
window.markNotificationAsRead = function(notificationId) {
    fetch(`/api/notifications/${notificationId}/mark-as-read`, {
        method: 'PATCH',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        }
    })
    .then(response => response.json())
    .then(data => {
        // Mettre à jour l'interface
        const item = document.querySelector(`[data-notification-id="${notificationId}"]`);
        if (item) {
            item.classList.remove('unread');
        }
        updateNotificationBadge();
    })
    .catch(error => {
        console.error('Erreur lors du marquage comme lu:', error);
    });
}

/**
 * Marquer toutes les notifications comme lues
 */
window.markAllNotificationsAsRead = function() {
    fetch('/api/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        }
    })
    .then(response => response.json())
    .then(data => {
        // Mettre à jour l'interface
        document.querySelectorAll('.notification-item').forEach(item => {
            item.classList.remove('unread');
        });
        updateNotificationBadge();
    })
    .catch(error => {
        console.error('Erreur lors du marquage comme lu:', error);
    });
}

/**
 * Exporter les fonctions pour utilisation globale
 */
window.NotificationManager = {
    updateBadge: updateNotificationBadge,
    showToast: showNotificationToast,
    markAsRead: window.markNotificationAsRead,
    markAllAsRead: window.markAllNotificationsAsRead,
};
