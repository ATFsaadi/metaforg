<?php
// Script pour synchroniser les photos de profil avec la table images
session_start();
include "includes/connexion.php";

$results = [];
$errors = [];

// Dossier contenant les photos de profil
$profileDir = __DIR__ . '/assets/images/Profil/';

// Vérifier que le dossier existe
if (!file_exists($profileDir)) {
    $errors[] = "Le dossier des photos de profil n'existe pas: $profileDir";
} else {
    // Lister tous les fichiers de profil
    $files = scandir($profileDir);
    $profilImages = [];
    
    // Filtrer pour ne garder que les images de profil
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && strpos($file, 'profil_') === 0) {
            $profilImages[] = $file;
        }
    }
    
    // Récupérer tous les utilisateurs
    $users = [];
    try {
        $stmt = $bdd->query("SELECT id_u, login FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errors[] = "Erreur lors de la récupération des utilisateurs: " . $e->getMessage();
    }
    
    // Pour chaque image de profil, vérifier si elle correspond à un utilisateur
    foreach ($profilImages as $image) {
        // Extraire l'ID de l'utilisateur depuis le nom du fichier
        // Format attendu: profil_12.png (où 12 est l'ID)
        if (preg_match('/profil_(\d+)\./', $image, $matches)) {
            $userId = $matches[1];
            
            // Chemin relatif de l'image (pour stockage en BDD)
            $imagePath = 'assets/images/Profil/' . $image;
            
            // Vérifier si l'utilisateur existe
            $userExists = false;
            $userName = '';
            foreach ($users as $user) {
                if ($user['id_u'] == $userId) {
                    $userExists = true;
                    $userName = $user['login'];
                    break;
                }
            }
            
            if ($userExists) {
                try {
                    // Vérifier d'abord si cette image existe déjà dans la table
                    $checkStmt = $bdd->prepare("
                        SELECT id_img FROM images 
                        WHERE u_id = ? AND chemin = ? AND type_img = 'profil'
                    ");
                    $checkStmt->execute([$userId, $imagePath]);
                    
                    if ($checkStmt->rowCount() === 0) {
                        // Ajouter l'image à la table images
                        $insertStmt = $bdd->prepare("
                            INSERT INTO images (u_id, nom, chemin, date_img, type_img) 
                            VALUES (?, ?, ?, NOW(), 'profil')
                        ");
                        $insertStmt->execute([$userId, "Photo de profil de $userName", $imagePath]);
                        
                        $results[] = "Image de profil '$image' ajoutée à la table images pour l'utilisateur $userName (ID: $userId)";
                    } else {
                        $results[] = "L'image de profil '$image' existe déjà dans la table pour l'utilisateur $userName";
                    }
                } catch (PDOException $e) {
                    $errors[] = "Erreur lors de l'ajout de l'image '$image': " . $e->getMessage();
                }
            } else {
                $errors[] = "Aucun utilisateur avec l'ID $userId trouvé pour l'image '$image'";
            }
        } else {
            $errors[] = "Format de nom de fichier incorrect: '$image'. Format attendu: 'profil_XX.png'";
        }
    }
}

// Sortie HTML
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Synchronisation des photos de profil</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
        .btn { display: inline-block; padding: 10px 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>Synchronisation des photos de profil avec la table images</h1>
    
    <?php if (!empty($errors)): ?>
        <h2>Erreurs rencontrées</h2>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($results)): ?>
        <h2>Opérations effectuées</h2>
        <div class="success">
            <ul>
                <?php foreach ($results as $result): ?>
                    <li><?php echo $result; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <h2>SQL pour mettre à jour manuellement</h2>
    <p>Vous pouvez utiliser cette requête SQL dans phpMyAdmin pour ajouter toutes les photos de profil à la table images :</p>
    <pre>
-- Requête SQL pour ajouter toutes les photos de profil à la table images
INSERT INTO images (u_id, nom, chemin, date_img, type_img)
SELECT 
    id_u as u_id,
    CONCAT('Photo de profil de ', login) as nom,
    CONCAT('assets/images/Profil/profil_', id_u, '.png') as chemin,
    NOW() as date_img,
    'profil' as type_img
FROM users u
WHERE NOT EXISTS (
    SELECT 1 FROM images i 
    WHERE i.u_id = u.id_u 
    AND i.chemin = CONCAT('assets/images/Profil/profil_', u.id_u, '.png')
    AND i.type_img = 'profil'
);
    </pre>
    
    <a href="home.php" class="btn">Retour à la page d'accueil</a>
</body>
</html>
