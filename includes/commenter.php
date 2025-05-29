<?php
// Démarrage de la session
session_start();

// Connexion à la base de données
include 'includes/connexion.php';

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    exit('Non autorisé');
}

// Récupération des données de la session et du formulaire
$user_id = $_SESSION['id_u'];
$publication_id = intval($_POST['publication_id'] ?? 0);
$contenu = trim($_POST['contenu'] ?? '');

//  du commentaire si le contenu n'est pas vide
if (!empty($contenu)) {
    $stmt = $bdd->prepare("INSERT INTO commentaires (user_id, publication_id, contenu) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $publication_id, $contenu]);
    echo 'OK';
} else {
    // Message d'erreur si le commentaire est vide
    echo 'Commentaire vide';
}
