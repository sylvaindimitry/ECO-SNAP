<?php
/**
 * Google OAuth Configuration
 * Pour obtenir ces identifiants :
 * 1. Va sur https://console.cloud.google.com/
 * 2. Crée un projet
 * 3. Active l'API Google+
 * 4. Crée des identifiants OAuth 2.0
 * 5. Configure les URIs de redirection
 */

return [
    'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_GOOGLE_CLIENT_ID',
    'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_GOOGLE_CLIENT_SECRET',
    'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/projet%20ecologique/auth/google/callback',
];
