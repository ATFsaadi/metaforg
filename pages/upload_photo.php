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
    // Récupère les informations de l'image
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];

        // Vérifie l'extension de l'image (formats autorisés)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            $errors[] = "Le fichier doit être une image (jpg, jpeg, png, gif).";
        }

        // Si l'extension est correcte, on génère un nom unique pour l'image
        if (empty($errors)) {
            $newFileName = 'photo_' . uniqid() . '.' . $fileExtension;
            $uploadDir = '../uploads/photos/'; // Le dossier où l'image sera enregistrée
            $destPath = $uploadDir . $newFileName;

            // Déplace le fichier dans le dossier uploads
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Enregistrement dans la base de données (optionnel)
                $stmt = $bdd->prepare("INSERT INTO publications (user_id, contenu, image) VALUES (:user_id, :contenu, :image)");
                $stmt->execute([
                    'user_id' => $_SESSION['id_u'],
                    'contenu' => 'Publication avec une image',
                    'image' => $newFileName
                ]);

                // Rediriger vers une page après l'upload
                header("Location: poster.php");
                exit;
            } else {
                $errors[] = "Erreur lors du téléchargement de l'image.";
            }
        }
    } else {
        $errors[] = "Aucune image sélectionnée ou erreur dans le téléchargement.";
    }
}

include "../includes/header-PG.php";
?>

<div class="container mt-5 pt-4">
    <h2>Uploader une photo</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?= implode('<br>', $errors) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" class="form-control" name="photo" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Publier la photo</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
