/**
 * ECO-SNAP - Main JavaScript
 * Gestion des notifications en temps réel via SSE
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les notifications SSE si l'utilisateur est connecté
    if (typeof isLoggedIn !== 'undefined' && isLoggedIn()) {
        initSSENotifications();
    }
    
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

/**
 * Initialiser les notifications en temps réel via Server-Sent Events
 */
function initSSENotifications() {
    // Vérifier si le navigateur supporte EventSource
    if (typeof(EventSource) === 'undefined') {
        console.warn('SSE non supporté par ce navigateur');
        return;
    }
    
    // URL du endpoint SSE
    const sseUrl = typeof sseEndpoint !== 'undefined' 
        ? sseEndpoint 
        : '/notifications/sse';
    
    // Créer la connexion SSE
    const eventSource = new EventSource(sseUrl);
    
    // Écouter les événements de type 'notification'
    eventSource.addEventListener('notification', function(event) {
        try {
            const data = JSON.parse(event.data);
            showNotificationToast(data);
            updateNotificationBadge();
        } catch (error) {
            console.error('Erreur de parsing SSE:', error);
        }
    });
    
    // Gérer les erreurs de connexion
    eventSource.addEventListener('error', function(error) {
        console.error('Erreur SSE:', error);
        
        // Reconnexion automatique après 5 secondes
        eventSource.close();
        setTimeout(function() {
            initSSENotifications();
        }, 5000);
    });
    
    // Stocker l'instance pour pouvoir la fermer si nécessaire
    window.sseConnection = eventSource;
}

/**
 * Afficher une notification toast
 */
function showNotificationToast(data) {
    // Créer le toast container s'il n'existe pas
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Créer le toast
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-info text-white">
                <i class="bi bi-bell me-2"></i>
                <strong class="me-auto">Nouvelle notification</strong>
                <small class="text-muted">À l'instant</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${escapeHtml(data.message || 'Nouvelle notification')}
                ${data.signalement_ville ? '<br><small class="text-muted">📍 ' + escapeHtml(data.signalement_ville) + '</small>' : ''}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Afficher le toast avec Bootstrap
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    toast.show();
    
    // Supprimer le toast après qu'il soit caché
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

/**
 * Mettre à jour le badge de notification dans la navbar
 */
function updateNotificationBadge() {
    fetch('/notifications/count-unread')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let badge = document.getElementById('notification-badge');
                
                if (data.count > 0) {
                    if (!badge) {
                        // Créer le badge s'il n'existe pas
                        const navLink = document.querySelector('a[href*="notifications"]');
                        if (navLink) {
                            badge = document.createElement('span');
                            badge.id = 'notification-badge';
                            badge.className = 'badge bg-danger';
                            navLink.appendChild(badge);
                        }
                    }
                    
                    if (badge) {
                        badge.textContent = data.count;
                    }
                } else if (badge) {
                    badge.remove();
                }
            }
        })
        .catch(error => console.error('Erreur mise à jour badge:', error));
}

/**
 * Échapper le HTML pour éviter les injections XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Marquer une notification comme lue
 */
function markNotificationAsRead(notificationId) {
    fetch('/notifications/' + notificationId + '/read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationBadge();
        }
    })
    .catch(error => console.error('Erreur:', error));
}

/**
 * Marquer toutes les notifications comme lues
 */
function markAllNotificationsAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationBadge();
            // Recharger la page pour mettre à jour l'affichage
            location.reload();
        }
    })
    .catch(error => console.error('Erreur:', error));
}

/**
 * Géolocalisation de l'utilisateur
 */
function getUserLocation() {
    return new Promise(function(resolve, reject) {
        if (!navigator.geolocation) {
            reject(new Error('Géolocalisation non supportée'));
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                resolve({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                });
            },
            function(error) {
                reject(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    });
}

/**
 * Formater une date en français
 */
function formatDateFr(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
