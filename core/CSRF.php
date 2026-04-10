<?php
/**
 * CSRF Protection Class
 * Protection contre les attaques CSRF
 */
class CSRF {
    /**
     * Générer un token CSRF
     */
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifier le token CSRF
     */
    public static function verifyToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Générer un champ hidden CSRF pour les formulaires
     */
    public static function field() {
        $token = self::generateToken();
        return "<input type='hidden' name='_csrf' value='{$token}'>";
    }

    /**
     * Middleware : Vérifier CSRF sur les requêtes POST
     */
    public static function protect() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_csrf'] ?? '';
            if (!self::verifyToken($token)) {
                http_response_code(403);
                die("Erreur de sécurité : token CSRF invalide.");
            }
        }
    }

    /**
     * Régénérer le token après utilisation
     */
    public static function regenerate() {
        unset($_SESSION['csrf_token']);
        return self::generateToken();
    }
}
