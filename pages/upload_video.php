<?php
session_start();
include "../includes/connexion.php"; // Connexion DB

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$errors = [];

// Traitement du formulaire d'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les informations de la vidéo
    if (isset($_FILES['video']) && $_FILES['video']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['video']['tmp_name'];
        $fileName = $_FILES['video']['name'];
        $fileSize = $_FILES['video']['size'];
        $fileType = $_FILES['video']['type'];

        // Vérifie l'extension de la vidéo (formats autorisés)
        $allowedExtensions = ['mp4', 'avi', 'mov', 'mkv'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            $errors[] = "Le fichier doit être une vidéo (mp4, avi, mov, mkv).";
        }

        // Si l'extension est correcte, on génère un nom unique pour la vidéo
        if (empty($errors)) {
            $newFileName = 'video_' . uniqid() . '.' . $fileExtension;
            $uploadDir = '../uploads/videos/'; // Le dossier où la vidéo sera enregistrée
            $destPath = $uploadDir . $newFileName;

            // Déplace le fichier dans le dossier uploads
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Enregistrement dans la base de données (optionnel)
                $stmt = $bdd->prepare("INSERT INTO publications (user_id, contenu, image) VALUES (:user_id, :contenu, :image)");
                $stmt->execute([
                    'user_id' => $_SESSION['id_u'],
                    'contenu' => 'Publication avec une vidéo',
                    'image' => $newFileName // Utilise la même colonne 'image' pour stocker les vidéos
                ]);

                // Rediriger vers une page après l'upload
                header("Location: poster.php");
                exit;
            } else {
                $errors[] = "Erreur lors du téléchargement de la vidéo.";
            }
        }
    } else {
        $errors[] = "Aucune vidéo sélectionnée ou erreur dans le téléchargement.";
    }
}

include "../includes/header-PG.php";
?>

<div class="container mt-5 pt-4">
    <h2>Uploader une vidéo</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?= implode('<br>', $errors) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" class="form-control" name="video" accept="video/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Publier la vidéo</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
