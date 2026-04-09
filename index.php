<?php
/**
 * Point d'entrée principal de l'application
 * Routing et bootstrapping
 */

// Démarrer la session
session_start();

// Configurer le timezone
$config = require __DIR__ . '/config/config.php';
date_default_timezone_set($config['timezone'] ?? 'Africa/Douala');

// Activer les erreurs en mode debug
if ($config['app_debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Autoload des classes core
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Router.php';

// Créer le routeur
$router = new Router($config['app_url']);

// ==================== ROUTES ====================

// Pages publiques
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');
$router->get('/about', 'HomeController@about');
$router->get('/faq', 'HomeController@faq');
$router->get('/mission', 'HomeController@mission');

// Authentification
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Dashboard
$router->get('/dashboard', 'HomeController@dashboard');

// Signalements
$router->get('/signalement/create', 'SignalementController@create');
$router->post('/signalement/store', 'SignalementController@store');
$router->get('/signalement/{id}', 'SignalementController@show');
$router->post('/signalement/{id}/assign', 'SignalementController@assign');
$router->post('/signalement/{id}/update-status', 'SignalementController@updateStatus');
$router->get('/signalements', 'SignalementController@index');

// API Signalements
$router->get('/api/signalements', 'SignalementController@apiIndex');
$router->get('/api/signalements/chauffeurs/{zoneId}', 'SignalementController@apiGetChauffeursDisponibles');

// Chauffeur
$router->get('/chauffeur/dashboard', 'ChauffeurController@dashboard');
$router->get('/chauffeur/signalements-disponibles', 'ChauffeurController@signalementsDisponibles');
$router->post('/chauffeur/signalement/{id}/prendre-en-charge', 'ChauffeurController@prendreEnCharge');
$router->get('/chauffeur/edit-planning', 'ChauffeurController@editPlanning');
$router->post('/chauffeur/save-planning', 'ChauffeurController@savePlanning');
$router->get('/chauffeur/statistiques', 'ChauffeurController@statistiques');

// Notifications
$router->get('/notifications', 'NotificationController@index');
$router->get('/notifications/sse', 'NotificationController@sseStream');
$router->post('/notifications/{id}/read', 'NotificationController@markAsRead');
$router->post('/notifications/read-all', 'NotificationController@markAllAsRead');
$router->get('/notifications/count-unread', 'NotificationController@countUnread');

// ==================== DISPATCH ====================

$router->dispatch();
