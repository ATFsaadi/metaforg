<?php
session_start();
include "../includes/connexion.php";

if (!isset($_SESSION['id_u']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$ami_id = intval($_GET['id']);
$user_id = $_SESSION['id_u'];

// Met à jour le statut en 'refuse'
$update = $bdd->prepare("
    UPDATE amis 
    SET statut = 'refuse'
    WHERE utilisateur_id = :ami_id 
    AND ami_id = :user_id 
    AND statut = 'en_attente'
");
$update->execute([
    'ami_id' => $ami_id,
    'user_id' => $user_id
]);

header("Location: amis.php");
exit;
?>
