<?php
ob_start();
session_start();
include "includes/connexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Vérification CSRF
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Tentative de hack !");
    }

    // 2. Validation du fichier
    $allowedTypes = ['image/jpeg', 'image/png'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['image']['tmp_name']);

    if (!in_array($mime, $allowedTypes)) {
        die("Seuls les JPEG/PNG sont autorisés");
    }

    // 3. Génération nom sécurisé
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid().'.'.$extension;
    $target = "uploads/".$filename;

    // 4. Déplacement sécurisé
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // 5. Insertion BDD sécurisée
        $insert = $bdd->prepare("
            INSERT INTO publications 
            (user_id, message, image) 
            VALUES (?, ?, ?)
        ");
        $insert->execute([
            $_SESSION['user']['id'],
            htmlspecialchars($_POST['message']),
            $filename
        ]);
        header("Location: fil.php?success=1");
    } else {
        die("Erreur lors de l'upload");
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier un message</title>
</head>
<body>

<h2>Publier un message</h2>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <textarea name="message" placeholder="Écrivez quelque chose..." required></textarea>
    <input type="file" name="image" accept="image/*">
    <button type="submit">Publier</button>
</form>

</body>
</html>
