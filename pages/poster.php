<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header.php";

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
    $target = "../uploads/".$filename;

    // 4. Déplacement sécurisé
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // 5. Insertion BDD sécurisée dans la table images
        $insert = $bdd->prepare("
            INSERT INTO images 
            (nom, chemin, u_id) 
            VALUES (?, ?, ?)
        ");
        $insert->execute([
            htmlspecialchars($_POST['message']),
            $filename,
            $_SESSION['id_u']
        ]);
        header("Location: ../home.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erreur lors de l'upload</div>";
    }
}
?>


<div class="container mt-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Publier un message</h2>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="p-3">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control" placeholder="Écrivez quelque chose..." required rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="imageFile" class="form-label">Choisir une image</label>
                            <input type="file" name="image" id="imageFile" class="form-control" accept="image/*" required>
                            <div class="form-text">Formats acceptés : JPEG, PNG.</div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-2"></i>Publier</button>
                            <a href="../home.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
