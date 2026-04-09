<?php
require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller {
    
    /**
     * Page d'accueil
     */
    public function index() {
        $this->view('home/index');
    }
    
    /**
     * Dashboard utilisateur
     */
    public function dashboard() {
        $this->requireAuth();
        
        $signalementModel = $this->model('Signalement');
        $zoneModel = $this->model('Zone');
        $notificationModel = $this->model('Notification');
        
        // Statistiques
        $stats = $signalementModel->getStatistiques();
        
        // Mes signalements
        $mesSignalements = $signalementModel->getAllWithDetails([
            'user_id' => $_SESSION['user_id']
        ]);
        
        // Notifications non lues
        $notificationsNonLues = $notificationModel->getNonLues($_SESSION['user_id']);
        
        // Zones
        $zones = $zoneModel->getWithSignalementsCount();
        
        $this->view('home/dashboard', [
            'stats' => $stats,
            'mesSignalements' => $mesSignalements,
            'notificationsNonLues' => $notificationsNonLues,
            'zones' => $zones
        ]);
    }
    
    /**
     * Page À propos
     */
    public function about() {
        $this->view('home/about');
    }
    
    /**
     * Page FAQ
     */
    public function faq() {
        $this->view('home/faq');
    }
    
    /**
     * Page Missions
     */
    public function mission() {
        $this->view('home/mission');
    }
}
