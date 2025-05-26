<?php
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifie la connexion
if (!isset($_SESSION['login'], $_SESSION['id_u'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupérer les amis acceptés
$query = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON (
        (users.id_u = amis.utilisateur_id AND amis.ami_id = :user_id)
        OR
        (users.id_u = amis.ami_id AND amis.utilisateur_id = :user_id)
    )
    WHERE amis.statut = 'accepte'
");
$query->execute(['user_id' => $user_id]);
$amis = $query->fetchAll();

// Récupérer les demandes envoyées en attente
$query_pending = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON users.id_u = amis.ami_id
    WHERE amis.utilisateur_id = :user_id
    AND amis.statut = 'en_attente'
");
$query_pending->execute(['user_id' => $user_id]);
$amis_pending = $query_pending->fetchAll();

// Récupérer les demandes reçues en attente
$query_received = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON users.id_u = amis.utilisateur_id
    WHERE amis.ami_id = :user_id
    AND amis.statut = 'en_attente'
");
$query_received->execute(['user_id' => $user_id]);
$amis_received = $query_received->fetchAll();
?>
<h2 class="section-title text-center">Vos amis</h2>

<div class="container">
  <div class="row g-4"> <!-- g-4 ajoute l'espace entre colonnes et rangées -->
    <!-- Bloc Amis -->
    <div class="publication-card col-md-4 p-3">
      <div class="post-header">Liste des amis</div>
      <ul class="post-content">
        <!-- contenu -->
      </ul>
    </div>

    <!-- Bloc Demandes envoyées -->
    <div class="publication-card col-md-4 p-3">
      <div class="post-header">Demandes envoyées</div>
      <ul class="post-content">
        <!-- contenu -->
      </ul>
    </div>

    <!-- Bloc Demandes reçues -->
    <div class="publication-card col-md-4 p-3">
      <div class="post-header">Demandes reçues</div>
      <ul class="post-content">
        <!-- contenu -->
      </ul>
    </div>
  </div>
</div>


<?php
include "../includes/mini-messagerie.php";
include '../includes/footer.php';
?>
