<?php
require_once __DIR__ . '/../core/Model.php';

class PlanningModel extends Model {
    protected $table = 'planning_travail';
    
    /**
     * Obtenir le planning d'un chauffeur
     */
    public function getByChauffeur($chauffeurId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE chauffeur_id = :chauffeur_id 
                AND actif = 1
                ORDER BY FIELD(jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')";
        return $this->db->fetchAll($sql, ['chauffeur_id' => $chauffeurId]);
    }
    
    /**
     * Vérifier si un chauffeur travaille un jour donné
     */
    public function travailleAujourdHui($chauffeurId, $jourSemaine) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE chauffeur_id = :chauffeur_id 
                AND jour_semaine = :jour 
                AND actif = 1";
        
        $result = $this->db->fetchOne($sql, [
            'chauffeur_id' => $chauffeurId,
            'jour' => strtolower($jourSemaine)
        ]);
        
        return $result['count'] > 0;
    }
    
    /**
     * Obtenir les chauffeurs qui travaillent aujourd'hui dans une zone
     */
    public function getChauffeursActifsAujourdhui($zoneId) {
        $jourFrancais = strtolower($this->getJourSemaineFrancais());
        
        $sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone,
                       pt.heure_debut, pt.heure_fin
                FROM chauffeurs c
                INNER JOIN users u ON c.user_id = u.id
                INNER JOIN {$this->table} pt ON c.id = pt.chauffeur_id
                WHERE c.zone_id = :zone_id
                AND c.statut = 'actif'
                AND pt.jour_semaine = :jour
                AND pt.actif = 1";
        
        return $this->db->fetchAll($sql, [
            'zone_id' => $zoneId,
            'jour' => $jourFrancais
        ]);
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
     * Ajouter un jour de travail
     */
    public function addJour($chauffeurId, $jour, $heureDebut = '08:00:00', $heureFin = '17:00:00') {
        $sql = "INSERT INTO {$this->table} (chauffeur_id, jour_semaine, heure_debut, heure_fin, actif)
                VALUES (:chauffeur_id, :jour, :heure_debut, :heure_fin, 1)
                ON DUPLICATE KEY UPDATE 
                    heure_debut = :heure_debut, 
                    heure_fin = :heure_fin, 
                    actif = 1";
        
        return $this->db->query($sql, [
            'chauffeur_id' => $chauffeurId,
            'jour' => strtolower($jour),
            'heure_debut' => $heureDebut,
            'heure_fin' => $heureFin
        ]);
    }
    
    /**
     * Désactiver un jour de travail
     */
    public function removeJour($chauffeurId, $jour) {
        $sql = "UPDATE {$this->table} 
                SET actif = 0 
                WHERE chauffeur_id = :chauffeur_id 
                AND jour_semaine = :jour";
        
        return $this->db->query($sql, [
            'chauffeur_id' => $chauffeurId,
            'jour' => strtolower($jour)
        ]);
    }
    
    /**
     * Obtenir les jours travaillés par un chauffeur sous forme de tableau
     */
    public function getJoursTravailles($chauffeurId) {
        $planning = $this->getByChauffeur($chauffeurId);
        return array_column($planning, 'jour_semaine');
    }
}
