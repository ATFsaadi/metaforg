<?php
// Démarrage de la session
session_start();

// === Connexion à la base de données 
include "connexion.php";

// Vérification de la session utilisateur
if (!isset($_SESSION['id_u'])) {
    echo json_encode(['new' => 0]);
    exit;
}

// Récupération de l'identifiant utilisateur connecté
$user_id = $_SESSION['id_u'];

// Requête : nombre de messages non lus pour l'utilisateur
$req = $bdd->prepare("SELECT COUNT(*) FROM envoyer WHERE id_recept = ? AND lu = 0");
$req->execute([$user_id]);
$count = $req->fetchColumn();

// Réponse en JSON avec le nombre de messages non lus
echo json_encode(['new' => $count]);
