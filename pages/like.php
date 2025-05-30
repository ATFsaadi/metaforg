<?php
require 'config.php';

if (!empty($_POST['id'])) {
    $id = (int) $_POST['id'];
    
 
    $stmt = $bdd->prepare("UPDATE publications SET likes = likes + 1 WHERE id = ?");
    $stmt->execute([$id]);

 
    $stmt = $bdd->prepare("SELECT likes FROM publications WHERE id = ?");
    $stmt->execute([$id]);
    $likes = $stmt->fetchColumn();

    echo $likes;
}
?>
