<?php
session_start();
include "../includes/connexion.php";

// Vérifications de sécurité
if (!isset($_SESSION['id_u']) || !isset($_POST['relation_id']) || !isset($_POST['ami_id'])) {
    header("Location: ../index.php");
    exit;
}

$relation_id = intval($_POST['relation_id']);
$ami_id = intval($_POST['ami_id']);
$user_id = $_SESSION['id_u'];

// Vérifie que la relation appartient bien à l'utilisateur
$check = $bdd->prepare("
    SELECT id FROM amis 
    WHERE id = ? 
    AND ((utilisateur_id = ? AND ami_id = ?)
    OR (utilisateur_id = ? AND ami_id = ?))
    AND statut = 'accepte'
");
$check->execute([$relation_id, $user_id, $ami_id, $ami_id, $user_id]);

if ($check->rowCount() > 0) {
    // Supprime la relation (permet de renvoyer une demande plus tard)
    $delete = $bdd->prepare("DELETE FROM amis WHERE id = ?");
    $delete->execute([$relation_id]);
    
    $_SESSION['success'] = "Ami supprimé avec succès";
} else {
    $_SESSION['error'] = "Action non autorisée";
}

// Redirection vers le profil
header("Location: profil.php?id=" . $ami_id);
exit;
?>