<?php
require_once "../includes/connexion.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion avec un message
    header('Location: ../index.php?error=Vous devez être connecté pour ajouter des amis');
    exit;
}

// Vérifier si l'ID de l'ami est fourni
if (!isset($_GET['friend_id']) || empty($_GET['friend_id'])) {
    header('Location: search.php?error=Utilisateur non spécifié');
    exit;
}

$user_id = $_SESSION['user_id'];
$friend_id = intval($_GET['friend_id']);

// Vérifier que l'utilisateur n'essaie pas de s'ajouter lui-même
if ($user_id == $friend_id) {
    header('Location: search.php?error=Vous ne pouvez pas vous ajouter vous-même');
    exit;
}

// Vérifier si l'utilisateur à ajouter existe
$stmt = $bdd->prepare("SELECT id_u FROM users WHERE id_u = ?");
$stmt->execute([$friend_id]);
if (!$stmt->fetch()) {
    header('Location: search.php?error=Utilisateur inexistant');
    exit;
}

// Simuler l'ajout d'ami en stockant dans la session
// Dans une vraie application, vous utiliseriez une table dans la base de données
if (!isset($_SESSION['friends'])) {
    $_SESSION['friends'] = [];
}

// Vérifier si l'ami est déjà dans la liste
if (in_array($friend_id, $_SESSION['friends'])) {
    header('Location: search.php?error=Cet utilisateur est déjà dans votre liste d\'amis');
    exit;
}

// Ajouter l'ami à la liste
$_SESSION['friends'][] = $friend_id;

// Rediriger vers la page de recherche avec un message de succès
header('Location: search.php?success=Utilisateur ajouté à votre liste d\'amis');
exit;
?>
