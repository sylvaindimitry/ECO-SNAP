<?php
require_once __DIR__ . '/../core/Controller.php';

class NotificationController extends Controller {
    
    /**
     * Endpoint SSE pour les notifications en temps réel
     * Cette méthode reste connectée et envoie les notifications au fur et à mesure
     */
    public function sseStream() {
        $this->requireAuth();
        
        // Désactier le buffering pour SSE
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Headers pour SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Désactiver le buffering nginx
        
        $userId = $_SESSION['user_id'];
        $notificationModel = $this->model('Notification');
        $lastNotificationId = 0;
        
        // Envoyer les notifications existantes
        $notifications = $notificationModel->getNonLues($userId);
        foreach ($notifications as $notif) {
            $this->sendEvent('notification', $notif);
            $lastNotificationId = max($lastNotificationId, $notif['id']);
        }
        
        // Garder la connexion ouverte et vérifier les nouvelles notifications
        while (connection_status() === CONNECTION_NORMAL) {
            // Vérifier les nouvelles notifications
            $sql = "SELECT * FROM notifications 
                    WHERE id > :last_id 
                    AND user_id = :user_id 
                    AND created_at > NOW() - INTERVAL 5 SECOND
                    ORDER BY created_at DESC";
            
            $newNotifications = $notificationModel->query($sql, [
                'last_id' => $lastNotificationId,
                'user_id' => $userId
            ]);
            
            foreach ($newNotifications as $notif) {
                $this->sendEvent('notification', $notif);
                $lastNotificationId = max($lastNotificationId, $notif['id']);
                
                // Marquer comme lue
                $notificationModel->markAsRead($notif['id']);
            }
            
            // Envoyer un commentaire pour garder la connexion active
            echo ": heartbeat\n\n";
            @ob_flush();
            @flush();
            
            // Attendre 2 secondes avant la prochaine vérification
            sleep(2);
        }
    }
    
    /**
     * Envoyer un événement SSE
     */
    private function sendEvent($event, $data) {
        echo "event: {$event}\n";
        echo "data: " . json_encode($data) . "\n\n";
        @ob_flush();
        @flush();
    }
    
    /**
     * Obtenir les notifications (API JSON)
     */
    public function index() {
        $this->requireAuth();
        
        $notificationModel = $this->model('Notification');
        $notifications = $notificationModel->getByUser($_SESSION['user_id'], 50);
        $countNonLues = $notificationModel->countNonLues($_SESSION['user_id']);
        
        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'non_lues_count' => $countNonLues
        ]);
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id) {
        $this->requireAuth();
        
        $notificationModel = $this->model('Notification');
        $notification = $notificationModel->find($id);
        
        // Vérifier que la notification appartient à l'utilisateur
        if ($notification['user_id'] != $_SESSION['user_id']) {
            $this->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }
        
        $notificationModel->markAsRead($id);
        
        $this->json(['success' => true]);
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead() {
        $this->requireAuth();
        
        $notificationModel = $this->model('Notification');
        $notificationModel->markAllAsRead($_SESSION['user_id']);
        
        $this->json(['success' => true]);
    }
    
    /**
     * Compter les notifications non lues
     */
    public function countUnread() {
        $this->requireAuth();
        
        $notificationModel = $this->model('Notification');
        $count = $notificationModel->countNonLues($_SESSION['user_id']);
        
        $this->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
