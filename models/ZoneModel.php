<?php
require_once __DIR__ . '/../core/Model.php';

class ZoneModel extends Model {
    protected $table = 'zones';
    
    /**
     * Trouver une zone par son nom et ville
     */
    public function findByNomEtVille($nom, $ville) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE LOWER(nom) = LOWER(:nom) 
                AND LOWER(ville) = LOWER(:ville) 
                LIMIT 1";
        return $this->db->fetchOne($sql, ['nom' => $nom, 'ville' => $ville]);
    }
    
    /**
     * Trouver ou créer une zone
     */
    public function findOrCreate($nom, $ville, $description = '') {
        $zone = $this->findByNomEtVille($nom, $ville);
        
        if ($zone) {
            return $zone;
        }
        
        $data = [
            'nom' => $nom,
            'ville' => $ville,
            'description' => $description
        ];
        
        $zoneId = $this->create($data);
        return $this->find($zoneId);
    }
    
    /**
     * Obtenir toutes les zones d'une ville
     */
    public function getByVille($ville) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE LOWER(ville) = LOWER(:ville) 
                ORDER BY nom ASC";
        return $this->db->fetchAll($sql, ['ville' => $ville]);
    }
    
    /**
     * Obtenir les zones avec le nombre de chauffeurs actifs
     */
    public function getWithChauffeursCount() {
        $sql = "SELECT z.*, 
                       COUNT(DISTINCT c.id) as chauffeurs_actifs
                FROM {$this->table} z
                LEFT JOIN chauffeurs c ON z.id = c.zone_id AND c.statut = 'actif'
                GROUP BY z.id
                ORDER BY z.ville, z.nom";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtenir les zones avec le nombre de signalements
     */
    public function getWithSignalementsCount() {
        $sql = "SELECT z.*, 
                       COUNT(DISTINCT s.id) as signalements_count,
                       SUM(CASE WHEN s.statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                       SUM(CASE WHEN s.statut = 'pris_en_charge' THEN 1 ELSE 0 END) as pris_en_charge
                FROM {$this->table} z
                LEFT JOIN signalements s ON z.id = s.zone_id
                GROUP BY z.id
                ORDER BY z.ville, z.nom";
        return $this->db->fetchAll($sql);
    }
}
