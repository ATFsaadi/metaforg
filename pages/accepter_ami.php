<?php
session_start();
include "../includes/connexion.php";

if (!isset($_SESSION['id_u']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$ami_id = intval($_GET['id']);
$user_id = $_SESSION['id_u'];

// Vérifie si la demande existe en attente
$check = $bdd->prepare("
    SELECT id FROM amis 
    WHERE utilisateur_id = ? 
    AND ami_id = ? 
    AND statut = 'en_attente'
");
$check->execute([$ami_id, $user_id]);

if ($check->rowCount() > 0) {
    // Accepter la demande
    $update = $bdd->prepare("
        UPDATE amis 
        SET statut = 'accepte' 
        WHERE utilisateur_id = ? 
        AND ami_id = ? 
        AND statut = 'en_attente'
    ");
    $update->execute([$ami_id, $user_id]);

    header("Location: notifications.php?success=accepted");
    exit;
} else {
    header("Location: notifications.php?error=notfound");
    exit;
}
?>
