<?php
/**
 * Controller Class - Base class for all controllers
 */
abstract class Controller {
    protected $db;
    protected $router;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Charger une vue
     */
    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            die("Vue '{$view}' non trouvée");
        }
        
        require_once $viewFile;
    }
    
    /**
     * Charger un modèle
     */
    protected function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        
        if (!file_exists($modelFile)) {
            die("Modèle '{$model}' non trouvé");
        }
        
        require_once $modelFile;
        
        $className = $model . 'Model';
        return new $className();
    }
    
    /**
     * Rediriger vers une URL
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Retourner une réponse JSON
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Vérifier le rôle de l'utilisateur
     */
    protected function hasRole($role) {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    /**
     * Middleware d'authentification
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }
    
    /**
     * Middleware de rôle
     */
    protected function requireRole($role) {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            http_response_code(403);
            die("Accès non autorisé");
        }
    }
    
    /**
     * Valider les données POST
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleParts = explode('|', $rule);
            
            foreach ($ruleParts as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field][] = "Le champ {$field} est requis";
                }
                
                if (strpos($r, 'min:') === 0) {
                    $min = (int) substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "Le champ {$field} doit contenir au moins {$min} caractères";
                    }
                }
                
                if ($r === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "Le champ {$field} doit être une adresse email valide";
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }
    
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
