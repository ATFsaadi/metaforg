<?php
// Ce script génère un avatar basé sur l'ID de l'utilisateur
// Usage: <img src="avatar.php?id=X"> où X est l'ID de l'utilisateur

// Vérifier si un ID est fourni
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Calculer l'index de l'avatar
$avatarIndex = ($user_id % 7) + 1;
if ($avatarIndex == 7) $avatarIndex = 8; // Gérer le cas spécial (pas d'avatar_7.png)

// Chemin absolu du dossier
$baseDir = dirname(__FILE__);

// Chemin de l'avatar avec chemin absolu
$avatarPath = "{$baseDir}/avatar/avatar_{$avatarIndex}.png";

// Vérifier si le fichier existe
if (!file_exists($avatarPath)) {
    // Si l'avatar n'existe pas, utiliser un avatar par défaut
    $avatarPath = "{$baseDir}/avatar/avatar_1.png";
}

// Définir les en-têtes pour l'image
header("Content-Type: image/png");
header("Cache-Control: max-age=86400"); // Cache pendant 24h

// Afficher l'image
readfile($avatarPath);
?>
