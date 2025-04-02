<?php
session_start();
include "includes/connexion.php";

if (!isset($_SESSION['login']) || !isset($_POST['publication_id'])) {
    header("Location: login.php");
    exit;
}

$commentaire = htmlspecialchars($_POST['commentaire']);
$publication_id = (int)$_POST['publication_id'];
$user_id = $_SESSION['id_u'];

$insert = $pdo->prepare("INSERT INTO commentaires (user_id, publication_id, commentaire) VALUES (:user_id, :publication_id, :commentaire)");
$insert->execute(['user_id' => $user_id, 'publication_id' => $publication_id, 'commentaire' => $commentaire]);

header("Location: fil.php");
exit;
?>
