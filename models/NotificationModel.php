<?php
require_once __DIR__ . '/../core/Model.php';

class NotificationModel extends Model {
    protected $table = 'notifications';
    
    /**
     * Créer une notification
     */
    public function createNotification($userId, $type, $message, $signalementId = null) {
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'signalement_id' => $signalementId,
            'lu' => 0
        ];
        
        return $this->create($data);
    }
    
    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function getNonLues($userId) {
        $sql = "SELECT n.*, s.ville as signalement_ville, s.quartier as signalement_quartier
                FROM {$this->table} n
                LEFT JOIN signalements s ON n.signalement_id = s.id
                WHERE n.user_id = :user_id 
                AND n.lu = 0
                ORDER BY n.created_at DESC";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    /**
     * Obtenir toutes les notifications d'un utilisateur
     */
    public function getByUser($userId, $limit = 50) {
        $sql = "SELECT n.*, s.ville as signalement_ville, s.quartier as signalement_quartier
                FROM {$this->table} n
                LEFT JOIN signalements s ON n.signalement_id = s.id
                WHERE n.user_id = :user_id
                ORDER BY n.created_at DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($notificationId) {
        return $this->update($notificationId, ['lu' => 1]);
    }
    
    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE {$this->table} SET lu = 1 WHERE user_id = :user_id AND lu = 0";
        return $this->db->query($sql, ['user_id' => $userId]);
    }
    
    /**
     * Compter les notifications non lues
     */
    public function countNonLues($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND lu = 0";
        $result = $this->db->fetchOne($sql, ['user_id' => $userId]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Notifier les chauffeurs d'un nouveau signalement
     * C'est la méthode clé pour les notifications par zone et jour
     */
    public function notifyChauffeursDeNouveauSignalement($signalementId, $zoneId) {
        // Obtenir le jour actuel en français
        $jourFrancais = $this->getJourSemaineFrancais();
        
        // Trouver tous les chauffeurs actifs dans la zone qui travaillent aujourd'hui
        $sql = "SELECT DISTINCT c.user_id
                FROM chauffeurs c
                INNER JOIN planning_travail pt ON c.id = pt.chauffeur_id
                WHERE c.zone_id = :zone_id
                AND c.statut = 'actif'
                AND pt.jour_semaine = :jour
                AND pt.actif = 1";
        
        $chauffeurs = $this->db->fetchAll($sql, [
            'zone_id' => $zoneId,
            'jour' => $jourFrancais
        ]);
        
        // Obtenir les détails du signalement
        $signalementModel = new SignalementModel();
        $signalement = $signalementModel->getSignalementDetails($signalementId);
        
        // Créer une notification pour chaque chauffeur
        $message = "Nouveau signalement à {$signalement['ville']}, {$signalement['quartier']} - Type: {$signalement['type_depot']}";
        
        foreach ($chauffeurs as $chauffeur) {
            $this->createNotification(
                $chauffeur['user_id'],
                'nouveau_signalement',
                $message,
                $signalementId
            );
        }
        
        return count($chauffeurs);
    }
    
    /**
     * Obtenir le jour de la semaine en français
     */
    private function getJourSemaineFrancais() {
        $jours = [
            'Sunday' => 'dimanche',
            'Monday' => 'lundi',
            'Tuesday' => 'mardi',
            'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi',
            'Friday' => 'vendredi',
            'Saturday' => 'samedi'
        ];
        
        $dayEnglish = date('l');
        return $jours[$dayEnglish] ?? 'lundi';
    }
    
    /**
     * Supprimer les anciennes notifications (plus de 7 jours)
     */
    public function cleanOldNotifications($days = 7) {
        $sql = "DELETE FROM {$this->table} 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        return $this->db->query($sql, ['days' => $days]);
    }
}
