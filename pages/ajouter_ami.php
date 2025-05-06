<?php

session_start();

include "../includes/connexion.php";

// 1. Vérifications de sécurité
if (!isset($_SESSION['id_u'])) {
    header("Location: login.php");
    exit;
}

/*if (!isset($_GET['id'], $_GET['csrf']) || 
    $_GET['csrf'] !== $_SESSION['csrf_token']) {
    die("Requête invalide");
} */
 
// 2. Validation des données
$friendId = (int)$_GET['id'];
if ($friendId <= 0) {
    die("ID invalide");
}

// 3. Empêcher l'auto-ajout
if ($friendId === $_SESSION['id_u']) {
    die("Action interdite");
}

// 4. Vérifier l'existence de l'utilisateur
$checkUser = $bdd->prepare("SELECT id_u FROM users WHERE id_u = ?");
$checkUser->execute([$friendId]);
if (!$checkUser->fetch()) {
    die("Utilisateur introuvable");
}

// 5. Vérifier les relations existantes
$checkRelation = $bdd->prepare("
    SELECT id, statut FROM amis 
    WHERE 
        (utilisateur_id = :user1 AND ami_id = :user2) OR 
        (ami_id = :user2 AND utilisateur_id = :user1)
");


$checkRelation->execute([
    'user1' => $_SESSION['id_u'], 
    'user2' => $friendId
]);

$existingRelation = $checkRelation->fetch();
//var_dump($_SESSION['id_u']); var_dump($friendId); die;
// 6. Gestion des différents cas
if ($existingRelation) {
    switch ($existingRelation['statut']) {
        case 'en_attente':
            $error = "Demande déjà envoyée";
            break;
        case 'accepted':
            $error = "Déjà ami avec cet utilisateur";
            break;
        case 'blocked':
            $error = "Action impossible";
            break;
        default:
            echo $existingRelation['statut'];
    }
    //header("Location: recherche.php?error=" . urlencode($error));
    //exit;
}

// 7. Création de la demande
try {
    $bdd->beginTransaction();
    
    // Insertion de la demande
    $insert = $bdd->prepare("
        INSERT INTO amis 
        (utilisateur, ami_id, status, date_ajout) 
        VALUES (?, ?, 'pending', NOW())
    ");
    $insert->execute([$_SESSION['id_u'], $friendId]);
    
    // Notification
    $notif = $bdd->prepare("
        INSERT INTO notifications
        (user_id, type, content, related_id, is_read)
        VALUES (?, 'friend_request', ?, ?, 0)
    ");
    $message = $_SESSION['user']['login'] . " vous a envoyé une demande d'ami";
    $notif->execute([$friendId, $message, $bdd->lastInsertId()]);
    
    $bdd->commit();
    
    header("Location: recherche.php?success=request_sent");
} catch (Exception $e) {
    $bdd->rollBack();
    header("Location: recherche.php?error=system_error");
}
exit;
?>