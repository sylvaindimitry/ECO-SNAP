<?php
/**
 * Helper functions
 * Fonctions utilitaires globales
 */

/**
 * Générer une URL
 */
function url($path = '') {
    $config = require __DIR__ . '/../config/config.php';
    return rtrim($config['app_url'], '/') . '/' . ltrim($path, '/');
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifier le rôle de l'utilisateur
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Obtenir l'ID de l'utilisateur connecté
 */
function userId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir le nom de l'utilisateur connecté
 */
function userName() {
    return $_SESSION['user_name'] ?? 'Invité';
}

/**
 * Rediriger vers une URL
 */
function redirect($url) {
    header("Location: {$url}");
    exit;
}

/**
 * Formater une date
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Formater un montant
 */
function formatMoney($amount) {
    return number_format($amount, 2, ',', ' ') . ' FCFA';
}

/**
 * Tronquer un texte
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Obtenir le jour de la semaine en français
 */
function getJourFrancais() {
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

/**
 * Obtenir les jours de la semaine
 */
function getJoursSemaine() {
    return ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
}

/**
 * Obtenir le statut avec badge Bootstrap
 */
function statutBadge($statut) {
    $badges = [
        'en_attente' => 'warning',
        'pris_en_charge' => 'info',
        'en_cours' => 'primary',
        'termine' => 'success',
        'annule' => 'danger'
    ];
    
    $labels = [
        'en_attente' => 'En attente',
        'pris_en_charge' => 'Pris en charge',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'annule' => 'Annulé'
    ];
    
    $color = $badges[$statut] ?? 'secondary';
    $label = $labels[$statut] ?? $statut;
    
    return "<span class='badge bg-{$color}'>{$label}</span>";
}
