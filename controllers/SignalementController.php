<?php
require_once __DIR__ . '/../core/Controller.php';

class SignalementController extends Controller {
    
    /**
     * Afficher le formulaire de signalement
     */
    public function create() {
        $this->requireAuth();
        
        $zoneModel = $this->model('Zone');
        $zones = $zoneModel->findAll();
        
        $this->view('signalements/create', [
            'zones' => $zones,
            'flash' => $this->getFlash()
        ]);
    }
    
    /**
     * Soumettre un signalement
     * LOGIQUE CLÉ: Filtrer les chauffeurs par zone et par jour de travail
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/signalement/create');
        }
        
        $this->requireAuth();
        
        // Récupérer les données
        $data = [
            'user_id' => $_SESSION['user_id'],
            'zone_id' => $_POST['zone_id'] ?? null,
            'ville' => trim($_POST['ville'] ?? ''),
            'quartier' => trim($_POST['quartier'] ?? ''),
            'type_depot' => $_POST['type_depot'] ?? '',
            'description' => trim($_POST['description'] ?? ''),
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'statut' => 'en_attente'
        ];
        
        // Validation
        if (empty($data['zone_id']) || empty($data['ville']) || empty($data['quartier'])) {
            $this->setFlash('error', 'Veuillez remplir tous les champs obligatoires');
            $this->redirect('/signalement/create');
        }
        
        // Gestion de la photo
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $photoPath = $this->uploadPhoto($_FILES['photo']);
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }
        }
        
        // Créer le signalement
        $signalementModel = $this->model('Signalement');
        $signalement = $signalementModel->createSignalement($data);
        
        // LOGIQUE CLÉ: Trouver les chauffeurs actifs dans la zone qui travaillent aujourd'hui
        $chauffeurModel = $this->model('Chauffeur');
        $notificationModel = $this->model('Notification');
        
        // Obtenir le jour actuel en français
        $jourFrancais = $this->getJourSemaineFrancais();
        
        // FILTRAGE PAR ZONE ET PAR JOUR
        $chauffeursDisponibles = $chauffeurModel->getActifsByZoneEtJour($data['zone_id'], $jourFrancais);
        
        // Si des chauffeurs sont disponibles, les notifier
        $chauffeursNotifies = 0;
        if (!empty($chauffeursDisponibles)) {
            // Créer une notification pour chaque chauffeur
            foreach ($chauffeursDisponibles as $chauffeur) {
                $message = "Nouveau signalement à {$data['ville']}, {$data['quartier']} - Type: {$data['type_depot']}";
                
                $notificationModel->createNotification(
                    $chauffeur['user_id'],
                    'nouveau_signalement',
                    $message,
                    $signalement['id']
                );
                
                $chauffeursNotifies++;
            }
        }
        
        $this->setFlash('success', "Signalement créé avec succès ! {$chauffeursNotifies} chauffeur(s) notifié(s).");
        $this->redirect('/signalement/' . $signalement['id']);
    }
    
    /**
     * Voir un signalement
     */
    public function show($id) {
        $this->requireAuth();
        
        $signalementModel = $this->model('Signalement');
        $signalement = $signalementModel->getSignalementDetails($id);
        
        if (!$signalement) {
            $this->setFlash('error', 'Signalement non trouvé');
            $this->redirect('/dashboard');
        }
        
        $this->view('signalements/show', ['signalement' => $signalement]);
    }
    
    /**
     * Lister tous les signalements (admin)
     */
    public function index() {
        $this->requireAuth();
        
        $signalementModel = $this->model('Signalement');
        $filters = [];
        
        // Filtres optionnels
        if (isset($_GET['zone_id'])) {
            $filters['zone_id'] = $_GET['zone_id'];
        }
        
        if (isset($_GET['statut'])) {
            $filters['statut'] = $_GET['statut'];
        }
        
        $signalements = $signalementModel->getAllWithDetails($filters);
        $zoneModel = $this->model('Zone');
        $zones = $zoneModel->findAll();
        
        $this->view('signalements/index', [
            'signalements' => $signalements,
            'zones' => $zones,
            'filters' => $filters
        ]);
    }
    
    /**
     * Assigner un chauffeur à un signalement (admin ou auto-assignation)
     */
    public function assign($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/signalement/' . $id);
        }
        
        $chauffeurId = $_POST['chauffeur_id'] ?? null;
        
        if (!$chauffeurId) {
            $this->setFlash('error', 'Veuillez sélectionner un chauffeur');
            $this->redirect('/signalement/' . $id);
        }
        
        // Vérifier que le chauffeur travaille aujourd'hui
        $chauffeurModel = $this->model('Chauffeur');
        $planningModel = $this->model('Planning');
        
        $jourFrancais = $this->getJourSemaineFrancais();
        if (!$planningModel->travailleAujourdHui($chauffeurId, $jourFrancais)) {
            $this->setFlash('error', 'Ce chauffeur ne travaille pas aujourd\'hui');
            $this->redirect('/signalement/' . $id);
        }
        
        // Assigner le chauffeur
        $signalementModel = $this->model('Signalement');
        $signalementModel->assignerChauffeur($id, $chauffeurId);
        
        // Notification au chauffeur
        $notificationModel = $this->model('Notification');
        $notificationModel->createNotification(
            $chauffeurId,
            'signalement_pris',
            "Vous avez été assigné au signalement",
            $id
        );
        
        $this->setFlash('success', 'Chauffeur assigné avec succès');
        $this->redirect('/signalement/' . $id);
    }
    
    /**
     * Mettre à jour le statut d'un signalement
     */
    public function updateStatus($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/signalement/' . $id);
        }
        
        $statut = $_POST['statut'] ?? '';
        
        if (!in_array($statut, ['en_attente', 'pris_en_charge', 'en_cours', 'termine', 'annule'])) {
            $this->setFlash('error', 'Statut invalide');
            $this->redirect('/signalement/' . $id);
        }
        
        $signalementModel = $this->model('Signalement');
        $signalementModel->updateStatut($id, $statut);
        
        $this->setFlash('success', 'Statut mis à jour');
        $this->redirect('/signalement/' . $id);
    }
    
    /**
     * API: Obtenir les signalements en JSON
     */
    public function apiIndex() {
        $this->requireAuth();
        
        $signalementModel = $this->model('Signalement');
        $filters = $_GET;
        
        $signalements = $signalementModel->getAllWithDetails($filters);
        
        $this->json([
            'success' => true,
            'data' => $signalements
        ]);
    }
    
    /**
     * API: Obtenir les chauffeurs disponibles pour une zone
     */
    public function apiGetChauffeursDisponibles($zoneId) {
        $this->requireAuth();
        
        $jourFrancais = $this->getJourSemaineFrancais();
        
        $chauffeurModel = $this->model('Chauffeur');
        $chauffeurs = $chauffeurModel->getActifsByZoneEtJour($zoneId, $jourFrancais);
        
        $this->json([
            'success' => true,
            'data' => $chauffeurs,
            'jour' => $jourFrancais
        ]);
    }
    
    /**
     * Uploader une photo
     */
    private function uploadPhoto($file) {
        $config = require __DIR__ . '/../config/config.php';
        $uploadDir = $config['upload_dir'];
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Vérifier le type de fichier
        $allowedTypes = $config['upload_allowed_types'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        // Vérifier la taille
        if ($file['size'] > $config['upload_max_size']) {
            return false;
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('signalement_') . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'uploads/' . $filename;
        }
        
        return false;
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
