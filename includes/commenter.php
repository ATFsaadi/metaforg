<?php
session_start();
include "includes/header.php";
include "connexion.php";

if (!isset($_SESSION['id_u'])) {
    header("Location: login.php");
    exit;
}

$postId = (int)$_GET['id'];
$postType = $_GET['type'];

// Récupérer le post
if ($postType === 'image') {
    $postQuery = $bdd->prepare("SELECT i.*, u.login FROM images i JOIN users u ON i.u_id = u.id_u WHERE i.id_img = ?");
} else {
    $postQuery = $bdd->prepare("SELECT p.*, u.login FROM publications p JOIN users u ON p.user_id = u.id_u WHERE p.id_p = ?");
}
$postQuery->execute([$postId]);
$post = $postQuery->fetch();

// Ajouter un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    
    if ($postType === 'image') {
        $insert = $bdd->prepare("INSERT INTO comment (id_img, id_u, contenu, is_like) VALUES (?, ?, ?, 0)");
    } else {
        $insert = $bdd->prepare("INSERT INTO comment (id_publication, id_u, contenu, is_like) VALUES (?, ?, ?, 0)");
    }
    $insert->execute([$postId, $_SESSION['id_u'], $comment]);
    
    header("Location: commenter.php?id=$postId&type=$postType");
    exit;
}

// Récupérer les commentaires
if ($postType === 'image') {
    $commentsQuery = $bdd->prepare("SELECT c.*, u.login FROM comment c JOIN users u ON c.id_u = u.id_u WHERE c.id_img = ? AND c.is_like = 0 ORDER BY c.date_c DESC");
} else {
    $commentsQuery = $bdd->prepare("SELECT c.*, u.login FROM comment c JOIN users u ON c.id_u = u.id_u WHERE c.id_publication = ? AND c.is_like = 0 ORDER BY c.date_c DESC");
}
$commentsQuery->execute([$postId]);
$comments = $commentsQuery->fetchAll();
?>

<div class="container">
    <!-- Affichage du post -->
    <div class="post">
        <!-- ... (même code que dans votre version actuelle) ... -->
    </div>
    
    <!-- Formulaire de commentaire -->
    <form method="post" class="comment-form">
        <textarea name="comment" required></textarea>
        <button type="submit">Commenter</button>
    </form>
    
    <!-- Liste des commentaires -->
    <div class="comments">
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <img src="assets/images/profil/profil_<?= $comment['id_u'] ?>">
                <div>
                    <strong><?= htmlspecialchars($comment['login']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($comment['contenu'])) ?></p>
                    <small><?= date('d/m/Y H:i', strtotime($comment['date_c'])) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>