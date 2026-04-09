<?php
/**
 * Configuration file
 * Paramètres de l'application
 */

return [
    // Base de données
    // En production (Render), les variables d'environnement sont utilisées
    'db_host' => getenv('DATABASE_HOST') ?: getenv('DB_HOST') ?: 'localhost',
    'db_name' => getenv('DATABASE_NAME') ?: getenv('DB_NAME') ?: 'ecosnap_mvc',
    'db_user' => getenv('DATABASE_USER') ?: getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DATABASE_PASSWORD') ?: getenv('DB_PASSWORD') ?: '',
    'db_port' => getenv('DATABASE_PORT') ?: getenv('DB_PORT') ?: 3306,
    'db_charset' => 'utf8mb4',

    // Application
    'app_name' => 'ECO-SNAP',
    'app_url' => getenv('APP_URL') ?: 'http://localhost/projet%20ecologique',
    'app_debug' => (bool) (getenv('APP_DEBUG') ?: true),

    // Session
    'session_lifetime' => 3600, // 1 heure

    // Upload
    'upload_dir' => __DIR__ . '/../uploads/',
    'upload_max_size' => 5 * 1024 * 1024, // 5MB
    'upload_allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],

    // Timezone
    'timezone' => 'Africa/Douala',
];
