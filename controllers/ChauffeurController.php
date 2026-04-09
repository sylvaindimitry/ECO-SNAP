<?php
require_once __DIR__ . '/../core/Controller.php';

class ChauffeurController extends Controller {
    
    /**
     * Dashboard du chauffeur
     */
    public function dashboard() {
        $this->requireRole('chauffeur');
        
        $chauffeurModel = $this->model('Chauffeur');
        $signalementModel = $this->model('Signalement');
        $planningModel = $this->model('Planning');
        
        // Obtenir les infos du chauffeur connecté
        $chauffeur = $chauffeurModel->getByUserId($_SESSION['user_id']);
        
        if (!$chauffeur) {
            $this->setFlash('error', 'Profil chauffeur non trouvé');
            $this->redirect('/login');
        }
        
        // Obtenir le planning
        $planning = $planningModel->getByChauffeur($chauffeur['id']);
        
        // Obtenir les signalements assignés
        $signalementsAssignes = $signalementModel->where(
            'chauffeur_id = :chauffeur_id AND statut IN ("pris_en_charge", "en_cours")',
            ['chauffeur_id' => $chauffeur['id']]
        );
        
        // Obtenir les signalements terminés
        $signalementsTermines = $signalementModel->where(
            'chauffeur_id = :chauffeur_id AND statut = "termine"',
            ['chauffeur_id' => $chauffeur['id']],
            'created_at DESC LIMIT 10'
        );
        
        // Obtenir les notifications non lues
        $notificationModel = $this->model('Notification');
        $notificationsNonLues = $notificationModel->getNonLues($_SESSION['user_id']);
        
        $this->view('chauffeurs/dashboard', [
            'chauffeur' => $chauffeur,
            'planning' => $planning,
            'signalementsAssignes' => $signalementsAssignes,
            'signalementsTermines' => $signalementsTermines,
            'notificationsNonLues' => $notificationsNonLues
        ]);
    }
    
    /**
     * Voir les signalements disponibles dans ma zone
     */
    public function signalementsDisponibles() {
        $this->requireRole('chauffeur');
        
        $chauffeurModel = $this->model('Chauffeur');
        $signalementModel = $this->model('Signalement');
        
        // Obtenir le chauffeur
        $chauffeur = $chauffeurModel->getByUserId($_SESSION['user_id']);
        
        // Obtenir les signalements en attente dans la zone du chauffeur
        $signalements = $signalementModel->getEnAttenteByZone($chauffeur['zone_id']);
        
        $this->view('chauffeurs/signalements_disponibles', [
            'chauffeur' => $chauffeur,
            'signalements' => $signalements
        ]);
    }
    
    /**
     * Prendre en charge un signalement
     */
    public function prendreEnCharge($id) {
        $this->requireRole('chauffeur');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/chauffeur/signalements-disponibles');
        }
        
        $chauffeurModel = $this->model('Chauffeur');
        $signalementModel = $this->model('Signalement');
        $planningModel = $this->model('Planning');
        
        // Obtenir le chauffeur
        $chauffeur = $chauffeurModel->getByUserId($_SESSION['user_id']);
        
        // Vérifier que le chauffeur travaille aujourd'hui
        $jourFrancais = $this->getJourSemaineFrancais();
        if (!$planningModel->travailleAujourdHui($chauffeur['id'], $jourFrancais)) {
            $this->setFlash('error', 'Vous ne travaillez pas aujourd\'hui');
            $this->redirect('/chauffeur/signalements-disponibles');
        }
        
        // Vérifier que le signalement est en attente
        $signalement = $signalementModel->find($id);
        if (!$signalement || $signalement['statut'] !== 'en_attente') {
            $this->setFlash('error', 'Ce signalement n\'est pas disponible');
            $this->redirect('/chauffeur/signalements-disponibles');
        }
        
        // Vérifier que le signalement est dans la zone du chauffeur
        if ($signalement['zone_id'] != $chauffeur['zone_id']) {
            $this->setFlash('error', 'Ce signalement n\'est pas dans votre zone');
            $this->redirect('/chauffeur/signalements-disponibles');
        }
        
        // Assigner le signalement au chauffeur
        $signalementModel->assignerChauffeur($id, $chauffeur['id']);
        
        $this->setFlash('success', 'Signalement pris en charge');
        $this->redirect('/chauffeur/dashboard');
    }
    
    /**
     * Modifier mon planning
     */
    public function editPlanning() {
        $this->requireRole('chauffeur');
        
        $chauffeurModel = $this->model('Chauffeur');
        $planningModel = $this->model('Planning');
        
        $chauffeur = $chauffeurModel->getByUserId($_SESSION['user_id']);
        $planning = $planningModel->getByChauffeur($chauffeur['id']);
        
        $flash = $this->getFlash();
        
        $this->view('chauffeurs/edit_planning', [
            'chauffeur' => $chauffeur,
            'planning' => $planning,
            'flash' => $flash
        ]);
    }
    
    /**
     * Sauvegarder le planning
     */
    public function savePlanning() {
        $this->requireRole('chauffeur');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/chauffeur/edit-planning');
        }
        
        $chauffeurModel = $this->model('Chauffeur');
        $planningModel = $this->model('Planning');
        
        $chauffeur = $chauffeurModel->getByUserId($_SESSION['user_id']);
        
        // Récupérer les jours sélectionnés
        $jours = $_POST['jours'] ?? [];
        
        if (empty($jours)) {
            $this->setFlash('error', 'Veuillez sélectionner au moins un jour');
            $this->redirect('/chauffeur/edit-planning');
        }
        
        // Formater les jours
        $joursFormates = [];
        foreach ($jours as $jour) {
            $joursFormates[] = [
                'jour' => $jour,
                'heure_debut' => $_POST['heure_debut_' . $jour] ?? '08:00:00',
                'heure_fin' => $_POST['heure_fin_' . $jour] ?? '17:00:00'
            ];
        }
        
        // Sauvegarder
        $planningModel->updatePlanning($chauffeur['id'], $joursFormates);
        
        $this->setFlash('success', 'Planning mis à jour');
        $this->redirect('/chauffeur/dashboard');
    }
    
    /**
     * Mes statistiques
     */
    public function statistiques() {
        $this->requireRole('chauffeur');
        
        $chauffeurModel = $this->model('Chauffeur');
        $chauffeur = $chauffeurModel->getByUserId($_SESSION['user_id']);
        
        $statistiques = $chauffeurModel->getStatistiques($chauffeur['id']);
        
        $this->view('chauffeurs/statistiques', [
            'chauffeur' => $chauffeur,
            'statistiques' => $statistiques
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
}
