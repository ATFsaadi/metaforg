<?php
session_start();
include "../includes/connexion.php";
if (!isset($_SESSION['id_u']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$ami_id = intval($_GET['id']);
$user_id = $_SESSION['id_u'];

// Vérifie si une demande d'ami en attente existe
$check = $bdd->prepare("
    SELECT id FROM amis 
    WHERE utilisateur_id = ? 
    AND ami_id = ? 
    AND statut = 'en_attente'
");
$check->execute([$ami_id, $user_id]);

// Si une demande existe, accepter l'invitation
if ($check->rowCount() > 0) {
    $update = $bdd->prepare("
        UPDATE amis 
        SET statut = 'accepte' 
        WHERE utilisateur_id = ? 
        AND ami_id = ? 
        AND statut = 'en_attente'
    ");
    $update->execute([$ami_id, $user_id]);

    header("Location: amis.php?success=accepted");
    exit;
// Sinon, rediriger avec une erreur
} else {
    header("Location: amis.php?error=notfound");
    exit;
}
?>
