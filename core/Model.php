<?php
/**
 * Model Class - Base class for all models
 */
abstract class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Trouver un enregistrement par son ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Trouver tous les enregistrements
     */
    public function findAll($orderBy = 'created_at DESC') {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Trouver avec condition WHERE
     */
    public function where($conditions, $params = [], $orderBy = 'created_at DESC') {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Créer un enregistrement
     */
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":{$field}";
        }, $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        return $this->db->insert($sql, $data);
    }
    
    /**
     * Mettre à jour un enregistrement
     */
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(', ', array_map(function($field) {
            return "{$field} = :{$field}";
        }, $fields));
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $data['id'] = $id;
        
        return $this->db->query($sql, $data);
    }
    
    /**
     * Supprimer un enregistrement
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }
    
    /**
     * Compter les enregistrements
     */
    public function count($conditions = '', $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Exécuter une requête personnalisée
     */
    public function query($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Exécuter une requête avec un seul résultat
     */
    public function queryOne($sql, $params = []) {
        return $this->db->fetchOne($sql, $params);
    }
}
