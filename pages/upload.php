<?php
session_start();
require_once '../includes/connexion.php'; // Connexion à la base de données

if (!isset($_SESSION['user_id'])) {
  die("Vous devez être connecté pour publier une story.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['story_image'])) {
  $uploadDir = 'assets/images/story/';
  $filename = uniqid() . '_' . basename($_FILES['story_image']['name']);
  $targetFile = $uploadDir . $filename;

  if (move_uploaded_file($_FILES['story_image']['tmp_name'], $targetFile)) {
    $stmt = $pdo->prepare("INSERT INTO stories (user_id, image_path) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $filename]);

    echo "Story publiée avec succès.";
    header("Location: index.php"); // Redirection après succès
  } else {
    echo "Erreur lors du téléchargement de l'image.";
  }
}
?>
