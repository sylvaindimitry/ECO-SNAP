<?php
require_once __DIR__ . '/../core/Model.php';

class UserModel extends Model {
    protected $table = 'users';
    
    /**
     * Trouver un utilisateur par email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        return $this->db->fetchOne($sql, ['email' => $email]);
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public function register($data) {
        // Hasher le mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $this->create($data);
    }
    
    /**
     * Authentifier un utilisateur
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Mettre à jour le profil
     */
    public function updateProfile($userId, $data) {
        // Ne pas permettre la modification du mot de passe ici
        unset($data['password']);
        
        return $this->update($userId, $data);
    }
    
    /**
     * Changer le mot de passe
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Vérifier si un email existe déjà
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] > 0;
    }
}
