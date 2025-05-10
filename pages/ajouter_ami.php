<?php
session_start();
include "../includes/connexion.php";

// Vérification de la requête
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id_u'], $_POST['ami_id'])) {
    $_SESSION['friend_message'] = [
        'type' => 'danger',
        'text' => 'Requête invalide'
    ];
    header("Location: ../index.php");
    exit;
}

$utilisateur_id = (int)$_SESSION['id_u'];
$ami_id = (int)$_POST['ami_id'];
$from_page = $_POST['from_page'] ?? 'profil.php';

try {
    // Empêcher de s'ajouter soi-même
    if ($utilisateur_id === $ami_id) {
        throw new Exception("Vous ne pouvez pas vous ajouter vous-même");
    }

    // Vérifier si la demande existe déjà
    $check = $bdd->prepare("SELECT * FROM amis 
                          WHERE (utilisateur_id = ? AND ami_id = ?) 
                          OR (utilisateur_id = ? AND ami_id = ?)");
    $check->execute([$utilisateur_id, $ami_id, $ami_id, $utilisateur_id]);

    if ($check->rowCount() > 0) {
        throw new Exception("Une demande existe déjà");
    }

    // Envoyer la demande
    $stmt = $bdd->prepare("INSERT INTO amis (utilisateur_id, ami_id, statut, date_demande) 
                          VALUES (?, ?, 'en_attente', NOW())");
    $stmt->execute([$utilisateur_id, $ami_id]);
    
    $_SESSION['friend_message'] = [
        'type' => 'success',
        'text' => '✅ Demande envoyée avec succès'
    ];

} catch (Exception $e) {
    $_SESSION['friend_message'] = [
        'type' => 'danger',
        'text' => '❌ ' . $e->getMessage()
    ];
}

header("Location: ../$from_page");
exit;
?>