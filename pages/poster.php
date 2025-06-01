<?php
session_start();
include "../includes/connexion.php";

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id_u'];
$errors = [];

// Traitement du formulaire avant toute sortie HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu'] ?? '');

    // Vérifie si le contenu n'est pas vide
    if (empty($contenu)) {
        $errors[] = "Le contenu de la publication ne peut pas être vide.";
    }

    // Gérer l'upload de l'image
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Récupère les informations de l'image
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        // Vérifie l'extension de l'image (format accepté)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            $errors[] = "Le fichier doit être une image (jpg, jpeg, png, gif).";
        }

        // Si l'extension est correcte, on génère un nom unique pour l'image
        if (empty($errors)) {
            $newFileName = 'img_' . uniqid() . '.' . $fileExtension;
            $uploadDir = '../uploads/'; // Le dossier où l'image sera enregistrée
            $destPath = $uploadDir . $newFileName;

            // Déplace le fichier dans le dossier uploads
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagePath = $newFileName; // On stocke le nom de l'image pour l'enregistrer dans la base de données
            } else {
                $errors[] = "Erreur lors du téléchargement de l'image.";
            }
        }
    }

    // Si aucun problème, insère la publication avec l'image dans la base de données
    if (empty($errors)) {
        $stmt = $bdd->prepare("INSERT INTO publications (user_id, contenu, image) VALUES (:user_id, :contenu, :image)");
        $stmt->execute([
            'user_id' => $user_id,
            'contenu' => $contenu,
            'image' => $imagePath // On enregistre le chemin de l'image
        ]);

        header("Location: poster.php"); // Redirige après la publication
        exit;
    }
}

include "../includes/header-PG.php";

// Récupération des publications
$req = $bdd->query("
    SELECT p.*, u.login 
    FROM publications p
    JOIN users u ON p.user_id = u.id_u
    ORDER BY p.date DESC
");
$publications = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 pt-4">
    <h2 class="section-title text-center">Publications</h2>

    <!-- Formulaire de publication -->
    <div class="card mb-4">
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', $errors) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <textarea name="contenu" class="form-control" rows="3" placeholder="Exprimez-vous..." required></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Ajouter une image</label><br>
                <input type="file" name="image" id="image" accept="image/*" class="d-none">
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <label for="image" class="btn btn-outline-primary m-0">
                        <i class="fas fa-upload"></i> Choisir une image
                    </label>

                    <button type="submit" class="btn btn-primary m-0">
                        Publier
                    </button>

                    <span id="file-name" class="text-muted"></span>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- Liste des publications -->
    <?php if ($publications): ?>
        <?php foreach ($publications as $pub): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        <?= htmlspecialchars($pub['login']) ?> • <?= date('d/m/Y H:i', strtotime($pub['date'])) ?>
                    </h6>
                    <p class="card-text"><?= nl2br(htmlspecialchars($pub['contenu'])) ?></p>

                    <?php if (!empty($pub['image'])): ?>
                        <div class="post-image">
                            <img src="../uploads/<?= htmlspecialchars($pub['image']) ?>" alt="Image de publication" class="img-fluid rounded">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">Aucune publication pour le moment.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
