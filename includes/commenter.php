<?php
session_start();
include 'includes/connexion.php';

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    exit('Non autorisé');
}

$user_id = $_SESSION['id_u'];
$publication_id = intval($_POST['publication_id'] ?? 0);
$contenu = trim($_POST['contenu'] ?? '');

if (!empty($contenu)) {
    $stmt = $bdd->prepare("INSERT INTO commentaires (user_id, publication_id, contenu) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $publication_id, $contenu]);
    echo 'OK';
} else {
    echo 'Commentaire vide';
}
