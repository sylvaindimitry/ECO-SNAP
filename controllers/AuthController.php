<?php
require_once __DIR__ . '/../core/Controller.php';

class AuthController extends Controller {
    
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        $flash = $this->getFlash();
        $this->view('auth/login', ['flash' => $flash]);
    }
    
    /**
     * Traiter la connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Veuillez remplir tous les champs');
            $this->redirect('/login');
        }
        
        // Authentification
        $userModel = $this->model('User');
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['nom'] . ' ' . $user['prenom'];
            
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
        $this->view('auth/register', ['flash' => $flash]);
    }
    
    /**
     * Traiter l'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }
        
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
                
                $this->setFlash('success', 'Inscription réussie ! Vous pouvez vous connecter.');
                $this->redirect('/login');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de l\'inscription');
                $this->redirect('/register');
            }
        } else {
            // Inscription normale (habitant)
            try {
                $userId = $userModel->register($data);
                
                $this->setFlash('success', 'Inscription réussie ! Vous pouvez vous connecter.');
                $this->redirect('/login');
            } catch (Exception $e) {
                $this->setFlash('error', 'Erreur lors de l\'inscription');
                $this->redirect('/register');
            }
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
