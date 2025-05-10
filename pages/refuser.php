<?php
session_start();
include "../includes/connexion.php";

// Vérification de la connexion et de l'ID reçu
if (!isset($_SESSION['connecte']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$ami_id = intval($_GET['id']);
$user_id = $_SESSION['id_u'];

// Deux options possibles pour le refus :

// OPTION 1 : Mettre simplement le statut à 'refuse'
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

// OPTION 2 : Supprimer complètement la relation (si vous préférez ne pas garder trace des refus)
/*
$delete = $bdd->prepare("
    DELETE FROM amis
    WHERE utilisateur_id = :ami_id 
    AND ami_id = :user_id 
    AND statut = 'en_attente'
");
$delete->execute([
    'ami_id' => $ami_id,
    'user_id' => $user_id
]);
*/

// Redirection vers la page des notifications
header("Location: notifications.php");
exit;
?>