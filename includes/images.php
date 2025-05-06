<?php
/**
 * Système de gestion d'images unifié pour Metaforge
 * Ce fichier regroupe toutes les fonctions liées aux images et avatars
 */

// Fonction pour générer un avatar basé sur l'ID utilisateur
function getAvatar($user_id = 1) {
    // Calculer l'index de l'avatar
    $avatarIndex = ($user_id % 7) + 1;
    if ($avatarIndex == 7) $avatarIndex = 8; // Gérer le cas spécial (pas d'avatar_7.png)

    // Chemin de l'avatar
    $avatarPath = dirname(__DIR__) . "/avatar/avatar_{$avatarIndex}.png";

    // Vérifier si le fichier existe
    if (!file_exists($avatarPath)) {
        // Si l'avatar n'existe pas, utiliser un avatar par défaut
        $avatarPath = dirname(__DIR__) . "/avatar/avatar_1.png";
    }

    return file_get_contents($avatarPath);
}

// Fonction pour servir un avatar en tant qu'image
function serveAvatar() {
    // Vérifier si un ID est fourni
    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
    
    // Définir les en-têtes pour l'image
    header("Content-Type: image/png");
    header("Cache-Control: max-age=86400"); // Cache pendant 24h
    
    // Afficher l'image
    echo getAvatar($user_id);
    exit;
}

// Fonction pour créer un avatar par défaut
function createDefaultAvatar($username = "MF") {
    // Dimensions de l'image
    $width = 200;
    $height = 200;

    // Créer une nouvelle image
    $image = imagecreatetruecolor($width, $height);

    // Couleurs
    $bg_color = imagecolorallocate($image, 30, 30, 30); // Fond sombre
    $border_color = imagecolorallocate($image, 0, 255, 0); // Bordure verte (#00FF00)
    $text_color = imagecolorallocate($image, 0, 191, 255); // Texte bleu

    // Remplir le fond
    imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

    // Dessiner un cercle
    imagefilledellipse($image, $width/2, $height/2, $width-20, $height-20, $border_color);
    imagefilledellipse($image, $width/2, $height/2, $width-40, $height-40, $bg_color);

    // Placer un texte (initiales de l'utilisateur)
    if (strlen($username) > 0) {
        $text = strtoupper(substr($username, 0, 1));
        if (strlen($username) > 1) {
            $words = explode(" ", $username);
            if (count($words) > 1) {
                $text = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            }
        }
    } else {
        $text = "MF";
    }

    // Centrer le texte
    $font_size = 5; // Taille de police
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    $text_x = ($width - $text_width) / 2;
    $text_y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $text_x, $text_y, $text, $text_color);

    // Retourner l'image en tant que chaîne de caractères
    ob_start();
    imagepng($image);
    $imageData = ob_get_clean();
    imagedestroy($image);
    
    return $imageData;
}

// Fonction pour encoder une image en base64
function imageToBase64($imagePath) {
    if (file_exists($imagePath)) {
        $imageData = file_get_contents($imagePath);
        $type = pathinfo($imagePath, PATHINFO_EXTENSION);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imageData);
        return $base64;
    }
    return null;
}

// Fonction pour redimensionner une image
function resizeImage($imagePath, $width, $height) {
    list($origWidth, $origHeight) = getimagesize($imagePath);
    $ratio = $origWidth / $origHeight;
    
    if ($width / $height > $ratio) {
        $width = $height * $ratio;
    } else {
        $height = $width / $ratio;
    }
    
    $image_p = imagecreatetruecolor($width, $height);
    
    $type = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    if ($type == 'jpeg' || $type == 'jpg') {
        $image = imagecreatefromjpeg($imagePath);
    } elseif ($type == 'png') {
        $image = imagecreatefrompng($imagePath);
    } elseif ($type == 'gif') {
        $image = imagecreatefromgif($imagePath);
    } else {
        return false;
    }
    
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
    
    ob_start();
    if ($type == 'jpeg' || $type == 'jpg') {
        imagejpeg($image_p, null, 90);
    } elseif ($type == 'png') {
        imagepng($image_p);
    } elseif ($type == 'gif') {
        imagegif($image_p);
    }
    $imageData = ob_get_clean();
    
    imagedestroy($image_p);
    imagedestroy($image);
    
    return $imageData;
}

// Gestion des routes pour ce fichier (s'il est appelé directement)
if (basename($_SERVER['SCRIPT_NAME']) == 'images.php') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'avatar':
            // Servir un avatar: images.php?action=avatar&id=123
            serveAvatar();
            break;
            
        case 'create':
            // Créer un avatar: images.php?action=create&name=John
            $name = isset($_GET['name']) ? $_GET['name'] : 'MF';
            header("Content-Type: image/png");
            echo createDefaultAvatar($name);
            exit;
            break;
            
        default:
            // Par défaut, retourner une erreur
            header("HTTP/1.0 404 Not Found");
            echo "Action non reconnue";
            exit;
    }
}
?>
