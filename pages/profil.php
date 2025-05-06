<?php
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Récupération des données utilisateur
$requete = $bdd->query("SELECT * FROM users WHERE id_u = " . $_GET['id']);
$reponse = $requete->fetch();
?>

<div class="profile-container">
    <div class="cover-photo">
        <div class="profile-photo"></div>
    </div>
    <div class="profile-details">
        <h2> Bienvenue <?= htmlspecialchars($reponse['login']) ?></h2>
        <p><strong>Email :</strong> <?= htmlspecialchars($reponse['email']) ?></p>
        <!-- Tu peux ajouter d'autres champs ici comme téléphone, bio, etc. -->
    </div>
</div>

<?php include '../includes/footer.php'; ?>
