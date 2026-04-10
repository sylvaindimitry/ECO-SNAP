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
     * Trouver un utilisateur par token de vérification
     */
    public function findByVerificationToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE verification_token = :token LIMIT 1";
        return $this->db->fetchOne($sql, ['token' => $token]);
    }

    /**
     * Trouver un utilisateur par Google ID
     */
    public function findByGoogleId($googleId) {
        $sql = "SELECT * FROM {$this->table} WHERE google_id = :google_id LIMIT 1";
        return $this->db->fetchOne($sql, ['google_id' => $googleId]);
    }

    /**
     * Trouver un utilisateur par remember token
     */
    public function findByRememberToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE remember_token = :token LIMIT 1";
        return $this->db->fetchOne($sql, ['token' => $token]);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function register($data) {
        // Hasher le mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Générer un token de vérification
        $data['verification_token'] = bin2hex(random_bytes(32));
        $data['email_verified'] = false;
        
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
     * Vérifier l'email d'un utilisateur
     */
    public function verifyEmail($userId) {
        $data = [
            'email_verified' => true,
            'verification_token' => null
        ];
        return $this->update($userId, $data);
    }

    /**
     * Sauvegarder le remember token
     */
    public function setRememberToken($userId, $token) {
        return $this->update($userId, ['remember_token' => $token]);
    }

    /**
     * Supprimer le remember token
     */
    public function clearRememberToken($userId) {
        return $this->update($userId, ['remember_token' => null]);
    }

    /**
     * Mettre à jour le last login
     */
    public function updateLastLogin($userId) {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * Authentifier avec Google (créer ou récupérer l'utilisateur)
     */
    public function authenticateWithGoogle($googleData) {
        // Vérifier si l'utilisateur existe déjà par Google ID
        $user = $this->findByGoogleId($googleData['google_id']);
        
        if ($user) {
            // Mettre à jour le last login
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        // Vérifier si l'email existe déjà
        $user = $this->findByEmail($googleData['email']);
        
        if ($user) {
            // Associer le Google ID à cet utilisateur
            $this->update($user['id'], [
                'google_id' => $googleData['google_id'],
                'email_verified' => true,
                'last_login' => date('Y-m-d H:i:s')
            ]);
            return $user;
        }
        
        // Créer un nouvel utilisateur
        $data = [
            'nom' => $googleData['family_name'] ?? $googleData['given_name'] ?? 'Utilisateur',
            'prenom' => $googleData['given_name'] ?? '',
            'email' => $googleData['email'],
            'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Mot de passe aléatoire
            'google_id' => $googleData['google_id'],
            'email_verified' => true,
            'role' => 'habitant',
            'last_login' => date('Y-m-d H:i:s')
        ];
        
        $userId = $this->create($data);
        return $this->find($userId);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile($userId, $data) {
        // Ne pas permettre la modification du mot de passe ici
        unset($data['password'], $data['google_id'], $data['verification_token']);
        
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

    /**
     * Compter les utilisateurs non vérifiés
     */
    public function countUnverified() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email_verified = 0";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
}
