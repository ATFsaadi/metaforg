<?php
session_start();
if (!empty($_FILES['media']['name'])) {
    if ($_FILES['media']['error'] !== UPLOAD_ERR_OK) {
        // Gestion améliorée des erreurs
        switch ($_FILES['media']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                die("Erreur : Le fichier dépasse la taille autorisée par le serveur.");
            case UPLOAD_ERR_FORM_SIZE:
                die("Erreur : Le fichier dépasse la taille spécifiée dans le formulaire.");
            default:
                die("Erreur lors du téléchargement. Code : " . $_FILES['media']['error']);
        }
    }

    // Vérifications du fichier
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
    $maxSize = 20 * 1024 * 1024; // 20 Mo
    $fileTmpName = $_FILES['media']['tmp_name'];
    $fileType = mime_content_type($fileTmpName);
    $fileSize = $_FILES['media']['size'];

    if (!in_array($fileType, $allowedTypes)) {
        die("Type de fichier non autorisé.");
    }

    if ($fileSize > $maxSize) {
        die("Le fichier dépasse la taille maximale de 20 Mo.");
    }

    // Création du répertoire si inexistant
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Génération du nom de fichier
    $extension = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
    $filename = uniqid('', true) . "." . $extension;
    $media = $target_dir . $filename;

    if (!move_uploaded_file($fileTmpName, $media)) {
        die("Erreur lors de l'enregistrement du fichier.");
    }
}
// Affichage des tailles autorisées pour vérifier la configuration
echo ini_get('upload_max_filesize') . "<br>";
echo ini_get('post_max_size') . "<br>";

// Inclure la connexion à la base de données (via PDO)
require_once "../includes/connexion.php";

// Vérification de la session utilisateur
if (!isset($_SESSION['id_u'])) {
    header('Location: login.php');
    exit();
}

// Traitement de la publication
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = htmlspecialchars(trim($_POST['contenu']), ENT_QUOTES, 'UTF-8');
    $media = null;

    // Vérifier si un fichier a été téléchargé
    if (!empty($_FILES['media']['name'])) {
        // Vérifier si le fichier a bien été uploadé sans erreur
        if ($_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
            $maxSize = 20 * 1024 * 1024;  // 20 MB
            $fileTmpName = $_FILES['media']['tmp_name']; // Récupère le chemin temporaire du fichier
            $fileType = mime_content_type($fileTmpName);  // Obtient le type MIME du fichier
            $fileSize = $_FILES['media']['size'];  // Taille du fichier

            // Vérification du type et de la taille du fichier
            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                $extension = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . "." . $extension;
                $target_dir = "uploads/";
                $media = $target_dir . $filename;

                // Déplacer le fichier téléchargé dans le répertoire cible
                if (!move_uploaded_file($fileTmpName, $media)) {
                    die("Erreur lors du téléchargement du fichier.");
                }
            } else {
                die("Type ou taille de fichier invalide.");
            }
        } else {
            $error = $_FILES['media']['error'];
            die("Erreur lors du téléchargement du fichier. Code erreur : $error");
        }
    }

    // Insérer la publication dans la base de données avec PDO
    $stmt = $bdd->prepare("INSERT INTO publications (user_id, contenu, media) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['id_u'], $contenu, $media]);
}

// Récupérer les publications
$stmt = $bdd->prepare("
    SELECT p.*, u.login 
    FROM publications p 
    JOIN users u ON p.user_id = u.id_u 
    ORDER BY p.date DESC
");
$stmt->execute();
$publications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publication</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; display: flex; justify-content: center; padding: 20px; }
        .container { width: 500px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        textarea, input[type='file'] { width: 100%; margin-bottom: 10px; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
        .submit-btn { width: 100%; background-color: #1877f2; color: white; padding: 10px; border: none; font-weight: bold; border-radius: 6px; cursor: pointer; }
        .post { border-top: 1px solid #ddd; margin-top: 20px; padding-top: 10px; }
        .post img, .post video { max-width: 100%; margin-top: 10px; border-radius: 8px; }
        .post h4 { margin: 0; }
        .post small { color: gray; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Créer une publication</h2>
        <form method="POST" enctype="multipart/form-data">
            <textarea name="contenu" rows="4" placeholder="Qu'avez-vous en tête ?" required></textarea>
            <input type="file" name="media">
            <button type="submit" class="submit-btn">Publier</button>
        </form>

        <h3>Fil d'actualité</h3>
        <?php foreach ($publications as $row): ?>
            <div class="post">
                <h4><?= htmlspecialchars($row['login']) ?></h4>
                <small><?= htmlspecialchars($row['date']) ?></small>
                <p><?= nl2br(htmlspecialchars($row['contenu'])) ?></p>
                <?php if (!empty($row['media'])): ?>
                    <?php if (str_contains($row['media'], '.mp4')): ?>
                        <video controls src="<?= htmlspecialchars($row['media']) ?>"></video>
                    <?php else: ?>
                        <img src="<?= htmlspecialchars($row['media']) ?>" alt="media">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
    </div>
</body>
</html>
