<?php
require_once __DIR__ . '/../core/Model.php';

class ChauffeurModel extends Model {
    protected $table = 'chauffeurs';
    
    /**
     * Obtenir les détails complets d'un chauffeur avec les infos utilisateur
     */
    public function getChauffeurDetails($chauffeurId) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone, z.nom as zone_nom, z.ville as zone_ville
                FROM {$this->table} c
                INNER JOIN users u ON c.user_id = u.id
                INNER JOIN zones z ON c.zone_id = z.id
                WHERE c.id = :id
                LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $chauffeurId]);
    }
    
    /**
     * Obtenir un chauffeur par son user_id
     */
    public function getByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        return $this->db->fetchOne($sql, ['user_id' => $userId]);
    }
    
    /**
     * Obtenir les chauffeurs actifs dans une zone donnée
     */
    public function getActifsByZone($zoneId) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone
                FROM {$this->table} c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.zone_id = :zone_id 
                AND c.statut = 'actif'
                ORDER BY u.nom";
        return $this->db->fetchAll($sql, ['zone_id' => $zoneId]);
    }
    
    /**
     * Obtenir les chauffeurs qui travaillent un jour donné dans une zone
     * C'est LA méthode clé pour le filtrage par jour et zone
     */
    public function getActifsByZoneEtJour($zoneId, $jourSemaine) {
        $sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone,
                       pt.heure_debut, pt.heure_fin
                FROM {$this->table} c
                INNER JOIN users u ON c.user_id = u.id
                INNER JOIN planning_travail pt ON c.id = pt.chauffeur_id
                WHERE c.zone_id = :zone_id 
                AND c.statut = 'actif'
                AND pt.jour_semaine = :jour
                AND pt.actif = 1
                ORDER BY u.nom";
        return $this->db->fetchAll($sql, [
            'zone_id' => $zoneId,
            'jour' => strtolower($jourSemaine)
        ]);
    }
    
    /**
     * Obtenir le planning d'un chauffeur
     */
    public function getPlanning($chauffeurId) {
        $sql = "SELECT * FROM planning_travail 
                WHERE chauffeur_id = :chauffeur_id 
                AND actif = 1
                ORDER BY FIELD(jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')";
        return $this->db->fetchAll($sql, ['chauffeur_id' => $chauffeurId]);
    }
    
    /**
     * Mettre à jour le planning d'un chauffeur
     */
    public function updatePlanning($chauffeurId, $jours) {
        // Supprimer l'ancien planning
        $sql = "UPDATE planning_travail SET actif = 0 WHERE chauffeur_id = :chauffeur_id";
        $this->db->query($sql, ['chauffeur_id' => $chauffeurId]);
        
        // Ajouter le nouveau planning
        foreach ($jours as $jour) {
            $sql = "INSERT INTO planning_travail (chauffeur_id, jour_semaine, heure_debut, heure_fin, actif)
                    VALUES (:chauffeur_id, :jour, :heure_debut, :heure_fin, 1)
                    ON DUPLICATE KEY UPDATE actif = 1, heure_debut = :heure_debut, heure_fin = :heure_fin";
            
            $this->db->query($sql, [
                'chauffeur_id' => $chauffeurId,
                'jour' => strtolower($jour['jour']),
                'heure_debut' => $jour['heure_debut'] ?? '08:00:00',
                'heure_fin' => $jour['heure_fin'] ?? '17:00:00'
            ]);
        }
        
        return true;
    }
    
    /**
     * Mettre à jour le statut d'un chauffeur
     */
    public function updateStatut($chauffeurId, $statut) {
        return $this->update($chauffeurId, ['statut' => $statut]);
    }
    
    /**
     * Obtenir les statistiques d'un chauffeur
     */
    public function getStatistiques($chauffeurId) {
        $sql = "SELECT 
                    COUNT(s.id) as total_signalements,
                    SUM(CASE WHEN s.statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(CASE WHEN s.statut = 'pris_en_charge' THEN 1 ELSE 0 END) as pris_en_charge,
                    SUM(CASE WHEN s.statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN s.statut = 'termine' THEN 1 ELSE 0 END) as termines
                FROM signalements s
                WHERE s.chauffeur_id = :chauffeur_id";
        
        return $this->db->fetchOne($sql, ['chauffeur_id' => $chauffeurId]);
    }
    
    /**
     * Créer un chauffeur avec son utilisateur
     */
    public function createChauffeur($userData, $chauffeurData) {
        $this->db->beginTransaction();
        
        try {
            // Créer l'utilisateur
            $userModel = new UserModel();
            $userId = $userModel->register($userData);
            
            // Créer le chauffeur
            $chauffeurData['user_id'] = $userId;
            $chauffeurId = $this->create($chauffeurData);
            
            $this->db->commit();
            return $chauffeurId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
