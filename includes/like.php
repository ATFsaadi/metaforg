<?php
session_start();
include "connexion.php";

if (!isset($_SESSION['id_u'])) {
    die(json_encode(['error' => 'Non connecté']));
}

$postId = (int)$_POST['post_id'];
$postType = $_POST['post_type']; // 'image' ou 'publication'
$userId = (int)$_SESSION['id_u'];

// Vérifier si like existe déjà
if ($postType === 'image') {
    $check = $bdd->prepare("SELECT id_c FROM comment WHERE id_img = ? AND id_u = ? AND is_like = 1");
} else {
    $check = $bdd->prepare("SELECT id_c FROM comment WHERE id_publication = ? AND id_u = ? AND is_like = 1");
}
$check->execute([$postId, $userId]);

if ($check->rowCount() > 0) {
    // Supprimer le like
    $delete = $bdd->prepare("DELETE FROM comment WHERE id_c = ?");
    $delete->execute([$check->fetchColumn()]);
    echo json_encode(['action' => 'unliked', 'count' => getLikeCount($bdd, $postId, $postType)]);
} else {
    // Ajouter le like
    if ($postType === 'image') {
        $insert = $bdd->prepare("INSERT INTO comment (id_img, id_u, contenu, is_like) VALUES (?, ?, '', 1)");
    } else {
        $insert = $bdd->prepare("INSERT INTO comment (id_publication, id_u, contenu, is_like) VALUES (?, ?, '', 1)");
    }
    $insert->execute([$postId, $userId]);
    echo json_encode(['action' => 'liked', 'count' => getLikeCount($bdd, $postId, $postType)]);
}

function getLikeCount($bdd, $postId, $postType) {
    if ($postType === 'image') {
        $count = $bdd->prepare("SELECT COUNT(*) FROM comment WHERE id_img = ? AND is_like = 1");
    } else {
        $count = $bdd->prepare("SELECT COUNT(*) FROM comment WHERE id_publication = ? AND is_like = 1");
    }
    $count->execute([$postId]);
    return $count->fetchColumn();
}
?>