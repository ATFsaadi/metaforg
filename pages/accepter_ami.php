<?php
session_start();
include "../includes/connexion.php";

// Vérifie si l'utilisateur est connecté ET si l'ID est présent
if (!isset($_SESSION['id_u']) || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$ami_id = intval($_GET['id']); // Sécurisation de l'ID
$user_id = $_SESSION['id_u'];

// 1. Vérifie d'abord si la demande existe
$check = $bdd->prepare("
    SELECT id FROM amis 
    WHERE utilisateur_id = ? 
    AND ami_id = ? 
    AND statut = 'en_attente'
");
$check->execute([$ami_id, $user_id]);

if ($check->rowCount() > 0) {
    // Option 1 : Marquer comme refusé (recommandé pour l'historique)
    $update = $bdd->prepare("
        UPDATE amis 
        SET statut = 'refuse' 
        WHERE utilisateur_id = ? 
        AND ami_id = ? 
        AND statut = 'en_attente'
    ");
    $update->execute([$ami_id, $user_id]);

    // Option 2 : Supprimer la demande (si pas besoin de garder une trace)
    // $delete = $bdd->prepare("DELETE FROM amis WHERE utilisateur_id = ? AND ami_id = ?");
    // $delete->execute([$ami_id, $user_id]);

    // Redirection après succès
    header("Location: notifications.php?success=refused");
    exit;
} else {
    // Si la demande n'existe pas ou a déjà été traitée
    header("Location: notifications.php?error=notfound");
    exit;
}
?>