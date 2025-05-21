<?php
session_start();
include "connexion.php";

if (!isset($_SESSION['id_u'])) {
    echo json_encode(['new' => 0]);
    exit;
}

$user_id = $_SESSION['id_u'];

$req = $bdd->prepare("SELECT COUNT(*) FROM envoyer WHERE id_recept = ? AND lu = 0");
$req->execute([$user_id]);
$count = $req->fetchColumn();

echo json_encode(['new' => $count]);
