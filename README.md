# ECO-SNAP - Gestion des Signalements de Dépôts d'Ordures

## 🌍 Présentation

Application web MVC en **PHP pur** (sans framework) pour la gestion des signalements de dépôts d'ordures avec notification en temps réel des chauffeurs/équipes de collecte.

### Fonctionnalités Clés

✅ **Architecture MVC complète** en PHP pur  
✅ **Filtrage intelligent des chauffeurs** par zone géographique ET par jour de travail  
✅ **Notifications en temps réel** via Server-Sent Events (SSE)  
✅ **Routing propre** avec URLs simplifiées  
✅ **Authentification sécurisée** avec gestion des rôles (habitant/chauffeur/admin)  
✅ **Géolocalisation** des signalements  
✅ **Upload de photos**  
✅ **Planning de travail** configurable par chauffeur  
✅ **Dashboard personnalisé** selon le rôle  
✅ **Interface responsive** avec Bootstrap 5  

---

## 📁 Architecture du Projet

```
projet ecologique/
├── assets/                 # Fichiers statiques (CSS, JS, images)
│   ├── css/
│   │   └── main.css       # Styles personnalisés
│   └── js/
│       └── main.js        # JavaScript + SSE notifications
├── config/
│   ├── config.php         # Configuration de l'application
│   └── database.sql       # Script SQL complet
├── controllers/           # Contrôleurs MVC
│   ├── AuthController.php         # Authentification
│   ├── ChauffeurController.php    # Espace chauffeur
│   ├── HomeController.php         # Pages publiques & dashboard
│   ├── NotificationController.php # Notifications SSE
│   └── SignalementController.php  # Gestion des signalements
├── core/                  # Classes fondamentales
│   ├── Controller.php     # Classe de base des contrôleurs
│   ├── Database.php       # Singleton PDO
│   ├── helpers.php        # Fonctions utilitaires
│   ├── Model.php          # Classe de base des modèles
│   └── Router.php         # Routeur URL
├── models/                # Modèles MVC
│   ├── ChauffeurModel.php
│   ├── NotificationModel.php
│   ├── PlanningModel.php
│   ├── SignalementModel.php
│   ├── UserModel.php
│   └── ZoneModel.php
├── uploads/               # Photos des signalements (créé automatiquement)
├── views/                 # Vues MVC
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── chauffeurs/
│   │   └── dashboard.php
│   ├── errors/
│   │   └── 404.php
│   ├── home/
│   │   ├── dashboard.php
│   │   └── index.php
│   ├── signalements/
│   │   └── create.php
│   └── layout.php         # Template principal
├── .htaccess              # Configuration Apache
├── index.php              # Point d'entrée principal (router)
└── README.md              # Ce fichier
```

---

## 🚀 Installation

### Prérequis

- **PHP** >= 7.4
- **MySQL** >= 5.7
- **Apache** avec mod_rewrite activé
- **WAMP/MAMP/XAMPP** ou serveur Linux

### Étape 1 : Base de données

1. Ouvrez phpMyAdmin ou votre client MySQL
2. Exécutez le script SQL :

```sql
-- Importer le fichier config/database.sql
-- OU exécuter en ligne de commande :
mysql -u root -p < config/database.sql
```

Le script va :
- Créer la base de données `ecosnap_mvc`
- Créer toutes les tables avec les relations
- Insérer des zones de test (Bonamoussadi, Bonaberi, etc.)
- Créer 2 utilisateurs de test

### Étape 2 : Configuration

Modifiez `config/config.php` si nécessaire :

```php
'db_host' => 'localhost',
'db_name' => 'ecosnap_mvc',
'db_user' => 'root',
'db_pass' => '',
```

### Étape 3 : Permissions

Assurez-vous que le dossier `uploads/` a les permissions d'écriture :

```bash
# Sur Linux/Mac
mkdir uploads
chmod 755 uploads

# Sur Windows (WAMP)
# Le dossier sera créé automatiquement
```

### Étape 4 : Activer mod_rewrite (Apache)

**Sur WAMP :**
1. Clic gauche sur l'icône WAMP
2. Apache → Apache modules → cocher `rewrite_module`
3. Redémarrer WAMP

**Sur Linux :**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Étape 5 : Accéder à l'application

```
http://localhost/projet%20ecologique/
```

---

## 👥 Comptes de Test

### Habitant
- **Email:** `habitant@ecosnap.com`
- **Mot de passe:** `password`
- **Rôle:** Peut créer des signalements

### Chauffeur
- **Email:** `chauffeur@ecosnap.com`
- **Mot de passe:** `password`
- **Rôle:** Peut recevoir des notifications et prendre en charge les signalements
- **Zone:** Bonamoussadi - Douala
- **Planning:** Lundi à Samedi

---

## 🎯 Fonctionnement du Filtrage des Chauffeurs

### Logique Métier

Quand un signalement est créé, le système :

1. **Identifie la zone** du signalement
2. **Détermine le jour actuel** (ex: "jeudi")
3. **Recherche les chauffeurs** qui :
   - Sont dans la **même zone** que le signalement
   - Ont un statut **"actif"**
   - **Travaillent ce jour-là** selon leur planning
   - Ont un planning **actif**

### Code Clé (SignalementController.php)

```php
// Obtenir le jour actuel en français
$jourFrancais = $this->getJourSemaineFrancais(); // ex: "jeudi"

// FILTRAGE PAR ZONE ET PAR JOUR
$chauffeursDisponibles = $chauffeurModel->getActifsByZoneEtJour(
    $data['zone_id'],    // Zone du signalement
    $jourFrancais        // Jour actuel
);

// Notifier chaque chauffeur disponible
foreach ($chauffeursDisponibles as $chauffeur) {
    $notificationModel->createNotification(
        $chauffeur['user_id'],
        'nouveau_signalement',
        $message,
        $signalement['id']
    );
}
```

### Requête SQL Optimisée

```sql
SELECT c.*, u.nom, u.prenom, u.email, u.telephone,
       pt.heure_debut, pt.heure_fin
FROM chauffeurs c
INNER JOIN users u ON c.user_id = u.id
INNER JOIN planning_travail pt ON c.id = pt.chauffeur_id
WHERE c.zone_id = :zone_id 
  AND c.statut = 'actif'
  AND pt.jour_semaine = :jour
  AND pt.actif = 1
ORDER BY u.nom
```

---

## 📡 Notifications en Temps Réel (SSE)

### Côté Serveur (NotificationController.php)

```php
public function sseStream() {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    // Vérifier les nouvelles notifications toutes les 2 secondes
    while (connection_status() === CONNECTION_NORMAL) {
        $newNotifications = $this->getNewNotifications();
        
        foreach ($newNotifications as $notif) {
            $this->sendEvent('notification', $notif);
        }
        
        echo ": heartbeat\n\n";
        @ob_flush();
        @flush();
        sleep(2);
    }
}
```

### Côté Client (assets/js/main.js)

```javascript
const eventSource = new EventSource('/notifications/sse');

eventSource.addEventListener('notification', function(event) {
    const data = JSON.parse(event.data);
    showNotificationToast(data);
    updateNotificationBadge();
});
```

### Avantages de SSE

✅ Connexion HTTP simple (pas de WebSocket)  
✅ Reconnexion automatique  
✅ Support natif dans les navigateurs modernes  
✅ Parfait pour les notifications unidirectionnelles  

---

## 🛣️ Routes Disponibles

### Pages Publiques
```
GET  /                          # Page d'accueil
GET  /about                     # À propos
GET  /faq                       # FAQ
GET  /mission                   # Missions
```

### Authentification
```
GET  /login                     # Formulaire connexion
POST /login                     # Traitement connexion
GET  /register                  # Formulaire inscription
POST /register                  # Traitement inscription
GET  /logout                    # Déconnexion
```

### Utilisateur Connecté
```
GET  /dashboard                 # Dashboard
GET  /signalement/create        # Formulaire signalement
POST /signalement/store         # Créer signalement
GET  /signalement/{id}          # Voir signalement
GET  /signalements              # Liste signalements
```

### Espace Chauffeur
```
GET  /chauffeur/dashboard              # Dashboard chauffeur
GET  /chauffeur/signalements-disponibles # Signalements à prendre
POST /chauffeur/signalement/{id}/prendre-en-charge
GET  /chauffeur/edit-planning          # Modifier planning
POST /chauffeur/save-planning          # Sauvegarder planning
GET  /chauffeur/statistiques           # Statistiques
```

### Notifications
```
GET  /notifications/sse         # Stream SSE temps réel
GET  /notifications             # Liste notifications (API)
POST /notifications/{id}/read   # Marquer comme lu
POST /notifications/read-all    # Tout marquer comme lu
GET  /notifications/count-unread # Compter non lues
```

### API
```
GET  /api/signalements                          # Liste JSON
GET  /api/signalements/chauffeurs/{zoneId}      # Chauffeurs dispos
```

---

## 🗄️ Structure de la Base de Données

### Tables Principales

**users** - Utilisateurs (habitants, chauffeurs, admins)
```sql
id, nom, prenom, email, telephone, password, role, created_at, updated_at
```

**zones** - Zones géographiques
```sql
id, nom, ville, description, created_at
```

**chauffeurs** - Informations spécifiques aux chauffeurs
```sql
id, user_id, zone_id, nom_equipe, vehicule_type, 
immatriculation, capacite, statut, created_at
```

**planning_travail** - Jours de travail des chauffeurs
```sql
id, chauffeur_id, jour_semaine, heure_debut, 
heure_fin, actif, created_at
```

**signalements** - Signalements de dépôts d'ordures
```sql
id, user_id, zone_id, ville, quartier, type_depot, 
description, photo, latitude, longitude, statut, 
chauffeur_id, date_signalement, created_at, updated_at
```

**notifications** - Notifications en temps réel
```sql
id, user_id, signalement_id, type, message, lu, created_at
```

---

## 🔐 Sécurité

✅ **Mots de passe hashés** avec `password_hash()` (bcrypt)  
✅ **Requêtes préparées** PDO (protection SQL injection)  
✅ **Validation des inputs** côté serveur  
✅ **Protection XSS** avec `htmlspecialchars()`  
✅ **Vérification des rôles** pour l'accès aux pages  
✅ **Sessions sécurisées** avec `session_start()`  
✅ **Headers de sécurité** dans .htaccess  
✅ **Protection uploads** (type, taille, nom unique)  

---

## 🎨 Technologies Utilisées

| Technologie | Usage |
|-------------|-------|
| **PHP 7.4+** | Backend, logique métier |
| **MySQL 5.7+** | Base de données |
| **PDO** | Connexion sécurisée à la BDD |
| **Bootstrap 5.3** | Framework CSS responsive |
| **Bootstrap Icons** | Icônes |
| **JavaScript Vanilla** | Frontend interactif |
| **SSE (EventSource)** | Notifications temps réel |
| **Apache mod_rewrite** | URLs propres |

---

## 📝 Utilisation

### 1. En tant qu'Habitant

1. **S'inscrire** ou se connecter
2. **Créer un signalement** :
   - Sélectionner la zone
   - Remplir ville, quartier
   - Choisir le type (terre/eau)
   - Ajouter une photo
   - Optionnel : géolocalisation
3. **Recevoir une confirmation** avec le nombre de chauffeurs notifiés
4. **Suivre l'évolution** du signalement dans le dashboard

### 2. En tant que Chauffeur

1. **S'inscrire** en sélectionnant :
   - Type de compte : Chauffeur
   - Zone d'intervention
   - Informations équipe/véhicule
2. **Configurer son planning** :
   - Aller dans "Mon planning"
   - Sélectionner les jours de travail
   - Définir les horaires
3. **Recevoir des notifications** en temps réel
4. **Prendre en charge** un signalement :
   - Voir les signalements disponibles dans sa zone
   - Cliquer sur "Prendre en charge"
   - Le système vérifie que le chauffeur travaille aujourd'hui
5. **Suivre ses statistiques**

---

## 🧪 Tests

### Tester le filtrage par zone et jour

1. Connectez-vous en tant que **chauffeur** (`chauffeur@ecosnap.com`)
2. Vérifiez le **planning** (doit inclure le jour actuel)
3. Déconnectez-vous
4. Connectez-vous en tant qu'**habitant** (`habitant@ecosnap.com`)
5. Créez un signalement dans la zone **Bonamoussadi**
6. Le système indique : **"X chauffeur(s) notifié(s)"**
7. Retournez sur le compte chauffeur : la **notification apparaît en temps réel**

### Tester sans notification

1. Modifiez le planning du chauffeur pour **retirer le jour actuel**
2. Créez un nouveau signalement
3. Le système indique : **"0 chauffeur(s) notifié(s)"**

---

## 🔧 Dépannage

### Erreur 404 sur toutes les pages sauf l'accueil
- Vérifiez que `mod_rewrite` est activé dans Apache
- Vérifiez que `.htaccess` est présent dans le dossier
- Dans `httpd.conf`, assurez-vous que `AllowOverride All` est défini

### Erreur de connexion BDD
- Vérifiez les identifiants dans `config/config.php`
- Vérifiez que la BDD `ecosnap_mvc` existe
- Vérifiez que MySQL est démarré

### Les notifications SSE ne fonctionnent pas
- Vérifiez que la session est active
- Ouvrez la console du navigateur (F12) pour voir les erreurs
- Vérifiez que le endpoint `/notifications/sse` retourne du texte/event-stream

### Erreur d'upload de photo
- Vérifiez que le dossier `uploads/` existe et a les permissions d'écriture
- Vérifiez la taille du fichier (max 5MB)
- Vérifiez le type (JPEG, PNG, GIF, WEBP uniquement)

---

## 📚 Ressources

- [Documentation PHP PDO](https://www.php.net/manual/fr/book.pdo.php)
- [Server-Sent Events MDN](https://developer.mozilla.org/fr/docs/Web/API/Server-sent_events)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Architecture MVC PHP](https://openclassrooms.com/fr/courses/1665806-architecture-mvc-en-php)

---

## 👨‍💻 Développement Futur

Améliorations possibles :

- [ ] Ajouter Google Maps pour la géolocalisation visuelle
- [ ] Export PDF des rapports
- [ ] Statistiques avancées avec graphiques
- [ ] Application mobile (React Native / Flutter)
- [ ] WebSocket au lieu de SSE pour du temps réel bidirectionnel
- [ ] Système de commentaire sur les signalements
- [ ] Notifications par email/SMS
- [ ] Multi-villes avec administrateurs régionaux
- [ ] API RESTful complète avec documentation Swagger

---

## 📄 Licence

Projet éducatif - ECO-SNAP © 2026

---

## 🤝 Contact

Pour toute question ou suggestion, n'hésitez pas à contacter l'équipe de développement.

---

**Développé avec ❤️ pour un environnement plus propre**
