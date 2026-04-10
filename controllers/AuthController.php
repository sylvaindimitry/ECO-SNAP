<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/Email.php';

class AuthController extends Controller {
    
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        // Vérifier le "remember me" cookie
        $this->checkRememberMe();
        
        $flash = $this->getFlash();
        CSRF::generateToken();
        $this->view('auth/login', [
            'flash' => $flash,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }
    
    /**
     * Traiter la connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        // Vérifier CSRF
        CSRF::protect();
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']);
        
        // Validation
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Veuillez remplir tous les champs');
            $this->redirect('/login');
        }
        
        // Authentification
        $userModel = $this->model('User');
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            // Vérifier si l'email est vérifié
            if (!$user['email_verified']) {
                $this->setFlash('warning', 'Veuillez vérifier votre adresse email avant de vous connecter. Vérifiez votre boîte de réception.');
                $this->redirect('/login');
                return;
            }
            
            // Créer la session
            $this->createUserSession($user, $rememberMe);
            
            // Mettre à jour last login
            $userModel->updateLastLogin($user['id']);
            
            // Régénérer le token CSRF
            CSRF::regenerate();
            
            // Marquer le first login pour le popup de bienvenue
            $_SESSION['show_welcome'] = true;
            
            // Redirection selon le rôle
            if ($user['role'] === 'chauffeur') {
                $this->redirect('/chauffeur/dashboard');
            } else {
                $this->redirect('/dashboard');
            }
        } else {
            $this->setFlash('error', 'Email ou mot de passe incorrect');
            $this->redirect('/login');
        }
    }
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegister() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        $flash = $this->getFlash();
        CSRF::generateToken();
        $this->view('auth/register', [
            'flash' => $flash,
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }
    
    /**
     * Traiter l'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
        // Vérifier CSRF
        CSRF::protect();
        
        // Récupérer les données
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'prenom' => trim($_POST['prenom'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'habitant'
        ];
        
        // Validation
        $validation = $this->validate($data, [
            'nom' => 'required|min:2',
            'prenom' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if ($validation !== true) {
            $this->setFlash('error', 'Veuillez corriger les erreurs');
            $this->redirect('/register');
        }
        
        // Vérifier si l'email existe déjà
        $userModel = $this->model('User');
        if ($userModel->emailExists($data['email'])) {
            $this->setFlash('error', 'Cet email est déjà utilisé');
            $this->redirect('/register');
        }
        
        // Si c'est un chauffeur, vérifier les infos supplémentaires
        if ($data['role'] === 'chauffeur') {
            // Créer le chauffeur avec son utilisateur
            $chauffeurData = [
                'zone_id' => $_POST['zone_id'] ?? null,
                'nom_equipe' => trim($_POST['nom_equipe'] ?? ''),
                'vehicule_type' => trim($_POST['vehicule_type'] ?? ''),
                'immatriculation' => trim($_POST['immatriculation'] ?? ''),
                'statut' => 'actif'
            ];
            
            if (empty($chauffeurData['zone_id'])) {
                $this->setFlash('error', 'Veuillez sélectionner une zone');
                $this->redirect('/register');
            }
            
            try {
                $chauffeurModel = $this->model('Chauffeur');
                $chauffeurId = $chauffeurModel->createChauffeur($data, $chauffeurData);
                
                // Envoyer l'email de vérification
                $user = $userModel->find($chauffeurId); // L'user_id est le même que le premier ID retourné
                // En fait, on doit récupérer l'user_id créé
                $user = $userModel->findByEmail($data['email']);
                Email::sendVerification($user['email'], $user['prenom'], $user['verification_token']);
                
                $this->setFlash('success', 'Inscription réussie ! Un email de vérification vous a été envoyé. Vérifiez votre boîte de réception.');
                $this->redirect('/login');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de l\'inscription');
                $this->redirect('/register');
            }
        } else {
            // Inscription normale (habitant)
            try {
                $userId = $userModel->register($data);
                $user = $userModel->find($userId);
                
                // Envoyer l'email de vérification
                Email::sendVerification($user['email'], $user['prenom'], $user['verification_token']);
                
                $this->setFlash('success', 'Inscription réussie ! Un email de vérification vous a été envoyé. Vérifiez votre boîte de réception.');
                $this->redirect('/login');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de l\'inscription');
                $this->redirect('/register');
            }
        }
    }
    
    /**
     * Vérification de l'email
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->setFlash('error', 'Token de vérification invalide');
            $this->redirect('/login');
            return;
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findByVerificationToken($token);
        
        if (!$user) {
            $this->setFlash('error', 'Token de vérification invalide ou expiré');
            $this->redirect('/login');
            return;
        }
        
        // Vérifier l'email
        $userModel->verifyEmail($user['id']);
        
        // Envoyer l'email de bienvenue
        Email::sendWelcome($user['email'], $user['prenom']);
        
        $this->setFlash('success', 'Email vérifié avec succès ! Vous pouvez maintenant vous connecter.');
        $this->redirect('/login');
    }

    /**
     * Renvoyer l'email de vérification
     */
    public function resendVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        CSRF::protect();
        
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $this->setFlash('error', 'Veuillez entrer votre email');
            $this->redirect('/login');
            return;
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            $this->setFlash('error', 'Email introuvable');
            $this->redirect('/login');
            return;
        }
        
        if ($user['email_verified']) {
            $this->setFlash('info', 'Votre email est déjà vérifié. Vous pouvez vous connecter.');
            $this->redirect('/login');
            return;
        }
        
        // Générer un nouveau token
        $newToken = bin2hex(random_bytes(32));
        $userModel->update($user['id'], ['verification_token' => $newToken]);
        
        // Renvoyer l'email
        Email::sendVerification($user['email'], $user['prenom'], $newToken);
        
        $this->setFlash('success', 'Email de vérification renvoyé ! Vérifiez votre boîte de réception.');
        $this->redirect('/login');
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        // Supprimer le cookie remember me
        if (isset($_COOKIE['remember_me'])) {
            $userModel = $this->model('User');
            $token = $_COOKIE['remember_me'];
            $user = $userModel->findByRememberToken($token);
            if ($user) {
                $userModel->clearRememberToken($user['id']);
            }
            setcookie('remember_me', '', time() - 3600, '/');
        }
        
        session_destroy();
        $this->redirect('/login');
    }

    /**
     * Google OAuth - Redirection
     */
    public function googleLogin() {
        $config = require __DIR__ . '/../config/google.php';
        
        $params = [
            'client_id' => $config['client_id'],
            'redirect_uri' => $config['redirect_uri'],
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        $this->redirect($authUrl);
    }

    /**
     * Google OAuth - Callback
     */
    public function googleCallback() {
        if (!isset($_GET['code'])) {
            $this->setFlash('error', 'Authentification Google annulée');
            $this->redirect('/login');
            return;
        }

        $config = require __DIR__ . '/../config/google.php';

        // Échanger le code contre un token d'accès
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code' => $_GET['code'],
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            $this->setFlash('error', 'Erreur lors de l\'authentification Google');
            $this->redirect('/login');
            return;
        }

        // Récupérer les informations de l'utilisateur
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $tokenData['access_token']
        ]);
        $userInfo = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userInfo, true);

        if (!$userData || !isset($userData['id'])) {
            $this->setFlash('error', 'Erreur lors de la récupération des informations Google');
            $this->redirect('/login');
            return;
        }

        // Authentifier ou créer l'utilisateur
        $userModel = $this->model('User');
        $user = $userModel->authenticateWithGoogle([
            'google_id' => $userData['id'],
            'email' => $userData['email'],
            'given_name' => $userData['given_name'] ?? '',
            'family_name' => $userData['family_name'] ?? ''
        ]);

        // Créer la session
        $this->createUserSession($user, false);
        $_SESSION['show_welcome'] = true;

        // Redirection
        if ($user['role'] === 'chauffeur') {
            $this->redirect('/chauffeur/dashboard');
        } else {
            $this->redirect('/dashboard');
        }
    }

    /**
     * Créer la session utilisateur
     */
    private function createUserSession($user, $rememberMe = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['nom'] . ' ' . $user['prenom'];
        $_SESSION['user_first_name'] = $user['prenom'];
        $_SESSION['user_last_name'] = $user['nom'];

        // Remember me
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $userModel = $this->model('User');
            $userModel->setRememberToken($user['id'], $token);
            
            // Cookie valide 30 jours
            setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        }
    }

    /**
     * Vérifier le cookie remember me
     */
    private function checkRememberMe() {
        if ($this->isAuthenticated()) {
            return;
        }

        if (!isset($_COOKIE['remember_me'])) {
            return;
        }

        $token = $_COOKIE['remember_me'];
        $userModel = $this->model('User');
        $user = $userModel->findByRememberToken($token);

        if ($user && $user['email_verified']) {
            $this->createUserSession($user, true);
            $this->redirect('/dashboard');
        } else {
            // Token invalide, supprimer le cookie
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }
}
