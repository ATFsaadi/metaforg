<?php
// Démarrage de la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Activation du rapport d'erreurs pour le développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclusion des fonctions utiles ===
require_once "function.php";

// Connexion à la base de données avec gestion des erreurs
try {
    $bdd = connexion('localhost', 'forum1', 'root', '');
} catch (PDOException $e) {
    die('Erreur BDD : ' . $e->getMessage());
}

// Génération d'un token CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
