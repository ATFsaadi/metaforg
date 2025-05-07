<?php
session_start();

include "../includes/connexion.php";
include '../includes/header-PG.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Tentative de hack !");
    }

    // 2. Vérification de la présence du fichier
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        die("Aucun fichier reçu ou une erreur est survenue.");
    }

    // 3. Vérification taille max
    $maxSize = 2 * 1024 * 1024; // 2 Mo
    if ($_FILES['image']['size'] > $maxSize) {
        die("Fichier trop volumineux (max 2 Mo)");
    }

    // 4. Vérification du type MIME réel
    $allowedTypes = ['image/jpeg', 'image/png'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['image']['tmp_name']);

    if (!in_array($mime, $allowedTypes)) {
        die("Seuls les fichiers JPEG ou PNG sont autorisés.");
    }

    // 5. Détermination de l'extension à partir du type MIME
    $extension = $mime === 'image/png' ? 'png' : 'jpg';
    $filename = uniqid('img_', true) . '.' . $extension;
    $uploadDir = 'uploads/';
    $target = $uploadDir . $filename;

    // 6. Création du dossier uploads si besoin
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 7. Déplacement du fichier
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // 8. Insertion dans la base de données
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

        // 9. Redirection vers la page principale
        header("Location: fil.php?success=1");
        exit;
    } else {
        die("Erreur lors de l'enregistrement du fichier.");
    }
}
?>


<h2>Publier un message</h2>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <textarea name="message" placeholder="Écrivez quelque chose..." required></textarea>
    <input type="file" name="image" accept="image/*">
    <button type="submit">Publier</button>
</form>

</body>
<?php include  '../includes/footer.php'; ?>
</html>
 