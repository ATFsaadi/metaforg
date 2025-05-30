<?php
session_start();

// Vérification de la connexion
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['id_u'];
$error = '';

// Connexion BDD
include '../includes/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['story_image'])) {
    $file = $_FILES['story_image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if ($file['error'] === UPLOAD_ERR_OK && in_array($file['type'], $allowedTypes)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = 'story_' . uniqid() . '.' . $ext;
        $uploadDir = '../assets/images/story/';
        $uploadPath = $uploadDir . $newName;

        // Création du dossier si nécessaire
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Insertion en BDD avec date d’expiration à 24h
            $stmt = $bdd->prepare("INSERT INTO stories (user_id, image_path, created_at, expire_at) 
                                   VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR))");
            $stmt->execute([$userId, $newName]);

            // Redirection
            header('Location: ../home.php');
            exit;
        } else {
            $error = "Erreur lors de l'upload du fichier.";
        }
    } else {
        $error = "Type de fichier non autorisé ou erreur d'upload.";
    }
}

// HTML
include '../includes/header-PG.php';
?>

<div class="container mt-5">
    <h1>Ajouter une story</h1>

    <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="story_image" class="form-label">Image de la story</label>
            <input
                type="file"
                name="story_image"
                id="story_image"
                accept="image/*"
                required="required"
                class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Publier la story</button>
        <a href="home.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>