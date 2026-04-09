<?php
require_once __DIR__ . '/../core/Model.php';

class SignalementModel extends Model {
    protected $table = 'signalements';
    
    /**
     * Créer un nouveau signalement
     */
    public function createSignalement($data) {
        $signalementId = $this->create($data);
        return $this->find($signalementId);
    }
    
    /**
     * Obtenir un signalement avec les détails complets
     */
    public function getSignalementDetails($signalementId) {
        $sql = "SELECT s.*, 
                       u.nom as user_nom, u.prenom as user_prenom, u.email as user_email, u.telephone as user_telephone,
                       z.nom as zone_nom, z.ville as zone_ville,
                       c.nom_equipe, c.vehicule_type,
                       ch_u.nom as chauffeur_nom, ch_u.prenom as chauffeur_prenom, ch_u.telephone as chauffeur_telephone
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN zones z ON s.zone_id = z.id
                LEFT JOIN chauffeurs c ON s.chauffeur_id = c.id
                LEFT JOIN users ch_u ON c.user_id = ch_u.id
                WHERE s.id = :id
                LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $signalementId]);
    }
    
    /**
     * Obtenir tous les signalements avec détails
     */
    public function getAllWithDetails($filters = []) {
        $sql = "SELECT s.*, 
                       u.nom as user_nom, u.prenom as user_prenom,
                       z.nom as zone_nom, z.ville as zone_ville,
                       c.nom_equipe,
                       ch_u.nom as chauffeur_nom, ch_u.prenom as chauffeur_prenom
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                INNER JOIN zones z ON s.zone_id = z.id
                LEFT JOIN chauffeurs c ON s.chauffeur_id = c.id
                LEFT JOIN users ch_u ON c.user_id = ch_u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['zone_id'])) {
            $sql .= " AND s.zone_id = :zone_id";
            $params['zone_id'] = $filters['zone_id'];
        }
        
        if (!empty($filters['statut'])) {
            $sql .= " AND s.statut = :statut";
            $params['statut'] = $filters['statut'];
        }
        
        if (!empty($filters['ville'])) {
            $sql .= " AND LOWER(z.ville) = LOWER(:ville)";
            $params['ville'] = $filters['ville'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND s.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        $sql .= " ORDER BY s.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Obtenir les signalements d'une zone
     */
    public function getByZone($zoneId, $limit = 50) {
        $sql = "SELECT s.*, u.nom as user_nom, u.prenom as user_prenom
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                WHERE s.zone_id = :zone_id
                ORDER BY s.created_at DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, [
            'zone_id' => $zoneId,
            'limit' => $limit
        ]);
    }
    
    /**
     * Obtenir les signalements en attente dans une zone
     */
    public function getEnAttenteByZone($zoneId) {
        $sql = "SELECT s.*, u.nom as user_nom, u.prenom as user_prenom
                FROM {$this->table} s
                INNER JOIN users u ON s.user_id = u.id
                WHERE s.zone_id = :zone_id
                AND s.statut = 'en_attente'
                ORDER BY s.created_at ASC";
        return $this->db->fetchAll($sql, ['zone_id' => $zoneId]);
    }
    
    /**
     * Assigner un chauffeur à un signalement
     */
    public function assignerChauffeur($signalementId, $chauffeurId) {
        $data = [
            'chauffeur_id' => $chauffeurId,
            'statut' => 'pris_en_charge',
            'date_prise_en_charge' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($signalementId, $data);
    }
    
    /**
     * Mettre à jour le statut d'un signalement
     */
    public function updateStatut($signalementId, $statut) {
        $data = ['statut' => $statut];
        
        if ($statut === 'termine') {
            $data['date_resolution'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($signalementId, $data);
    }
    
    /**
     * Obtenir les statistiques des signalements
     */
    public function getStatistiques() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN statut = 'pris_en_charge' THEN 1 ELSE 0 END) as pris_en_charge,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'termine' THEN 1 ELSE 0 END) as termines,
                    SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) as annules
                FROM {$this->table}";
        
        return $this->db->fetchOne($sql);
    }
    
    /**
     * Obtenir les signalements récents (pour notifications)
     */
    public function getRecents($minutes = 30) {
        $sql = "SELECT s.*, z.nom as zone_nom, z.ville as zone_ville
                FROM {$this->table} s
                INNER JOIN zones z ON s.zone_id = z.id
                WHERE s.created_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
                ORDER BY s.created_at DESC";
        return $this->db->fetchAll($sql, ['minutes' => $minutes]);
    }
}
