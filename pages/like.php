<?php
require 'config.php'; // ou ta connexion à la base de données

if (!empty($_POST['id'])) {
    $id = (int) $_POST['id'];
    
    // Ajouter un like à la publication
    $stmt = $bdd->prepare("UPDATE publications SET likes = likes + 1 WHERE id = ?");
    $stmt->execute([$id]);

    // Récupérer le nouveau nombre de likes
    $stmt = $bdd->prepare("SELECT likes FROM publications WHERE id = ?");
    $stmt->execute([$id]);
    $likes = $stmt->fetchColumn();

    echo $likes;
}
?>
