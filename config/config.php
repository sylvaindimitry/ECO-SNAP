<?php
/**
 * Configuration file
 * Paramètres de l'application
 */

return [
    // Base de données
    'db_host' => 'localhost',
    'db_name' => 'ecosnap_mvc',
    'db_user' => 'root',
    'db_pass' => '',
    'db_charset' => 'utf8mb4',
    
    // Application
    'app_name' => 'ECO-SNAP',
    'app_url' => 'http://localhost/projet%20ecologique',
    'app_debug' => true,
    
    // Session
    'session_lifetime' => 3600, // 1 heure
    
    // Upload
    'upload_dir' => __DIR__ . '/../uploads/',
    'upload_max_size' => 5 * 1024 * 1024, // 5MB
    'upload_allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    
    // Timezone
    'timezone' => 'Africa/Douala',
];
