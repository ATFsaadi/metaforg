<?php
// Ce script génère un avatar basé sur l'ID de l'utilisateur
// Usage : <img src="avatar.php?id=X"> où X est l'ID de l'utilisateur

// Vérifier si un ID est fourni, sinon utiliser 1 par défaut
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Liste des avatars valides (exclut l'avatar_7.png qui n'existe pas)
$validIndexes = [1, 2, 3, 4, 5, 6, 8, 9, 10, 11];

// Calcul de l'index basé sur l'ID utilisateur
$avatarIndex = $validIndexes[$user_id % count($validIndexes)];

// Déterminer le chemin absolu du dossier courant
$baseDir = dirname(__FILE__);

// Construire le chemin complet vers le fichier avatar
$avatarPath = "{$baseDir}/avatar/avatar_{$avatarIndex}.png";

// Vérifier si le fichier existe, sinon utiliser un avatar par défaut
if (!file_exists($avatarPath)) {
    $avatarPath = "{$baseDir}/avatar/avatar_1.png";

    // Si même l'avatar par défaut est manquant, retourner une erreur
    if (!file_exists($avatarPath)) {
        http_response_code(404);
        exit("Avatar non trouvé.");
    }
}

// Définir les en-têtes pour l'image PNG
header("Content-Type: image/png");
header("Cache-Control: max-age=86400"); // Cache pendant 24 heures

// Envoyer le fichier image au navigateur
readfile($avatarPath);
?>
