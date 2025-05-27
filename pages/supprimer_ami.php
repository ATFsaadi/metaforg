<?php
session_start();
include "../includes/connexion.php";

if (!isset($_POST['relation_id'], $_SESSION['id_u'])) {
    header("Location: ../index.php");
    exit;
}

$relation_id = (int)$_POST['relation_id'];
$current_user_id = $_SESSION['id_u'];

// On récupère les infos de la relation
$amiStmt = $bdd->prepare("SELECT utilisateur_id, ami_id FROM amis WHERE id = ?");
$amiStmt->execute([$relation_id]);
$relation = $amiStmt->fetch();

if (!$relation) {
    $_SESSION['friend_message'] = [
        'type' => 'danger',
        'text' => "Relation introuvable"
    ];
    header("Location: ../index.php");
    exit;
}

// Identifier l’ami pour rediriger vers son profil
$ami_id = ($relation['utilisateur_id'] == $current_user_id) ? $relation['ami_id'] : $relation['utilisateur_id'];

try {
    // Supprimer la relation
    $stmt = $bdd->prepare("DELETE FROM amis WHERE id = ? AND (utilisateur_id = ? OR ami_id = ?)");
    $stmt->execute([$relation_id, $current_user_id, $current_user_id]);

    $_SESSION['friend_message'] = [
        'type' => 'success',
        'text' => '❌ Ami supprimé avec succès'
    ];
} catch (Exception $e) {
    $_SESSION['friend_message'] = [
        'type' => 'danger',
        'text' => "Erreur : " . $e->getMessage()
    ];
}

header("Location: profil.php?id=" . $ami_id);
exit;
