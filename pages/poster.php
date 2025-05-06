<?php
session_start();

include "../includes/connexion.php";
include '../includes/header-PG.php';

// Générer un token CSRF s'il n'existe pas déjà
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Variable pour stocker les messages
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $messages[] = [
            'type' => 'danger',
            'text' => 'Tentative de hack détectée!'
        ];
    } 
    // 2. Vérification de la présence du fichier
    elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $messages[] = [
            'type' => 'danger',
            'text' => 'Aucun fichier reçu ou une erreur est survenue.'
        ];
    } 
    else {
        // 3. Vérification taille max
        $maxSize = 8 * 1024 * 1024; // 8 Mo
        
        if ($_FILES['image']['size'] > $maxSize) {
            $messages[] = [
                'type' => 'danger',
                'text' => 'Fichier trop volumineux (max 8 Mo). Veuillez choisir une image plus petite.'
            ];
        } 
        else {
            // 4. Vérification du type MIME réel
            $allowedTypes = ['image/jpeg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime, $allowedTypes)) {
                $messages[] = [
                    'type' => 'danger',
                    'text' => 'Seuls les fichiers JPEG ou PNG sont autorisés.'
                ];
            } 
            else {
                // 5. Traitement de l'image
                $extension = $mime === 'image/png' ? 'png' : 'jpg';
                $filename = 'img_' . uniqid() . '.' . $extension;
                
                // Traitement de l'image (redimensionnement/compression si nécessaire)
                try {
                    // Créer une ressource d'image
                    if ($mime === 'image/jpeg') {
                        $image = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                    } else {
                        $image = imagecreatefrompng($_FILES['image']['tmp_name']);
                    }
                    
                    if (!$image) {
                        throw new Exception("Impossible de traiter l'image");
                    }
                    
                    // Obtenir les dimensions de l'image
                    $width = imagesx($image);
                    $height = imagesy($image);
                    
                    // Redimensionner si trop grande
                    if ($width > 1200 || $height > 1200) {
                        $ratio = $width / $height;
                        
                        if ($width > $height) {
                            $newWidth = 1200;
                            $newHeight = (int)($newWidth / $ratio);
                        } else {
                            $newHeight = 1200;
                            $newWidth = (int)($newHeight * $ratio);
                        }
                        
                        // S'assurer que les dimensions ne sont pas nulles
                        $newWidth = max($newWidth, 1);
                        $newHeight = max($newHeight, 1);
                        
                        $newImage = imagecreatetruecolor($newWidth, $newHeight);
                        
                        // Préserver la transparence pour les PNG
                        if ($mime === 'image/png') {
                            imagealphablending($newImage, false);
                            imagesavealpha($newImage, true);
                            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                        }
                        
                        // Redimensionner
                        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($image);
                        $image = $newImage;
                    }
                    
                    // Préparer le dossier de destination conforme à la structure du projet
                    $uploadDir = '../assets/images/uploads/';
                    
                    // S'assurer que le dossier existe en créant tous les dossiers nécessaires
                    if (!file_exists($uploadDir)) {
                        if (!mkdir($uploadDir, 0777, true)) {
                            // Si impossible de créer, essayer de créer chaque dossier séparément
                            if (!file_exists('../assets')) {
                                mkdir('../assets', 0777);
                            }
                            if (!file_exists('../assets/images')) {
                                mkdir('../assets/images', 0777);
                            }
                            if (!mkdir($uploadDir, 0777)) {
                                throw new Exception("Impossible de créer le dossier de destination");
                            }
                        }
                    }
                    
                    // Vérifier et ajuster les permissions de tous les dossiers parents
                    @chmod('../assets', 0777);
                    @chmod('../assets/images', 0777);
                    @chmod($uploadDir, 0777);
                    
                    // Vérifier si le dossier est maintenant accessible en écriture
                    if (!is_writable($uploadDir)) {
                        throw new Exception("Le dossier de destination n'est pas accessible en écriture. Chemin: " . realpath($uploadDir));
                    }
                    
                    // Sauvegarder l'image
                    $targetFile = $uploadDir . $filename;
                    $success = false;
                    
                    if ($mime === 'image/jpeg') {
                        $success = imagejpeg($image, $targetFile, 85);
                    } else {
                        $success = imagepng($image, $targetFile, 6);
                    }
                    
                    // Libérer la mémoire
                    imagedestroy($image);
                    
                    if (!$success) {
                        throw new Exception("Impossible de sauvegarder l'image");
                    }
                    
                    // Enregistrer dans la base de données
                    $insert = $bdd->prepare("
                        INSERT INTO images
                        (nom, chemin, date_img, u_id) 
                        VALUES (?, ?, NOW(), ?)
                    ");
                    
                    $result = $insert->execute([
                        htmlspecialchars($_POST['message']),
                        'assets/images/uploads/' . $filename,
                        $_SESSION['id_u']
                    ]);
                    
                    if (!$result) {
                        throw new Exception("Erreur lors de l'enregistrement dans la base de données");
                    }
                    
                    // Succès
                    $messages[] = [
                        'type' => 'success',
                        'text' => 'Image téléchargée avec succès!'
                    ];
                    
                    // Redirection après 1,5 secondes
                    echo "<script>setTimeout(function() { window.location.href = '../home.php?success=1'; }, 1500);</script>";
                } 
                catch (Exception $e) {
                    $messages[] = [
                        'type' => 'danger',
                        'text' => 'Erreur: ' . $e->getMessage()
                    ];
                }
            }
        }
    }
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h2 class="mb-0"><i class="fas fa-pencil-alt me-2"></i> Publier un message</h2>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="alert alert-<?= $message['type'] ?>">
                                <?= $message['text'] ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea 
                                name="message" 
                                id="message" 
                                class="form-control" 
                                placeholder="Écrivez quelque chose..." 
                                rows="3"
                                required
                            ></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input 
                                type="file" 
                                name="image" 
                                id="image" 
                                class="form-control" 
                                accept="image/jpeg, image/png"
                                required
                            >
                            <div class="form-text text-muted">
                                Formats acceptés: JPEG, PNG. Taille maximale: 8 Mo.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i> Publier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
