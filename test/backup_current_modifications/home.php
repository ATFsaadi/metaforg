<?php
ob_start();
session_start();
include "includes/header.php";
require_once 'includes/connexion.php';
require_once 'includes/function.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: index.php");
    exit;
}

// Récupérer les publications (images) de la base de données avec base64_data si disponible
$query = "SELECT i.id_img as id_pub, i.nom as titre, i.chemin, i.date_img as date, 
          u.login, u.id_u as user_id, i.base64_data 
          FROM images i 
          JOIN users u ON i.u_id = u.id_u 
          ORDER BY i.date_img DESC LIMIT 20";
$statement = $bdd->prepare($query);
try {
    $statement->execute();
    $publications = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $publications = [];
}

// Suggestions d'utilisateurs
try {
    $req_users = $bdd->prepare("
        SELECT u.id_u, u.login 
        FROM users u
        WHERE u.id_u != :user_id
        LIMIT 5
    ");
    $req_users->bindValue(':user_id', $_SESSION['id_u'], PDO::PARAM_INT);
    $req_users->execute();
    $suggestions = $req_users->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $suggestions = [];
}
?>
<div class="container mt-5 pt-4">
    <div class="row">
        <!-- Sidebar gauche -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sidebar">
                <div class="list-group mb-4">
                    <a
                        href="./pages/profil.php?id=<?= $_SESSION['id_u'] ?>"
                        class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>
                        Mon profil
                    </a>
                    <a href="./pages/amis.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-friends me-2"></i>
                        Mes amis
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-gamepad me-2"></i>
                        Jeux
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i>
                        Groupes
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Événements
                    </a>
                </div>
            </div>
        </div>

        <!-- Fil d'actualité -->
        <div class="col-lg-6">
            <!-- Stories -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-2">
                    <div class="d-flex overflow-auto" style="gap: 15px; padding-bottom: 10px;">
                        <!-- Créer une story -->
                        <div class="text-center" style="min-width: 90px;">
                            <div class="position-relative">
                                <div
                                    class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                    style="width: 90px; height: 90px; overflow: hidden;">
                                    <img
                                        src="avatar.php?id=<?= $_SESSION['id_u'] ?>"
                                        alt="Votre avatar"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                        loading="lazy">
                                    <div
                                        class="position-absolute bg-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px; bottom: 5px; right: 5px; border: 3px solid #3b5998;">
                                        <i class="fas fa-plus text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-1 small">Créer une story</div>
                        </div>

                        <!-- Stories amis -->
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="text-center" style="min-width: 90px;">
                            <div class="position-relative">
                                <div
                                    class="rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 90px; height: 90px; overflow: hidden; border: 3px solid #3b5998;">
                                    <img
                                        src="avatar.php?id=<?= $i ?>"
                                        alt="Avatar ami <?= $i ?>"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                        loading="lazy">
                                </div>
                            </div>
                            <div class="mt-1 small">Ami
                                <?= $i ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Créer une publication -->
            <div class="create-post mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-2">
                        <img
                            src="avatar.php?id=<?= $_SESSION['id_u'] ?>"
                            alt="Avatar"
                            class="post-avatar"
                            loading="lazy">
                    </div>
                    <input
                        type="text"
                        class="create-post-input form-control"
                        placeholder="Quoi de neuf, <?= htmlspecialchars($_SESSION['login']) ?>?"
                        onclick="window.location.href='./pages/poster.php'">
                </div>
                <div class="d-flex justify-content-between">
                    <button
                        class="btn btn-outline-secondary"
                        onclick="window.location.href='./pages/poster.php'">
                        <i class="fas fa-image me-1"></i>
                        Photo
                    </button>
                    <button
                        class="btn btn-outline-secondary"
                        onclick="window.location.href='./pages/poster.php'">
                        <i class="fas fa-video me-1"></i>
                        Vidéo
                    </button>
                    <button
                        class="btn btn-outline-secondary"
                        onclick="window.location.href='./pages/poster.php'">
                        <i class="fas fa-smile me-1"></i>
                        Humeur
                    </button>
                </div>
            </div>

            <!-- Publications -->
            <?php foreach ($publications as $post): ?>
            <div class="post-card mb-4">
                <div class="post-header d-flex align-items-center">
                    <img
                            src="avatar.php?id=<?= $post['user_id'] ?>"
                            alt="Avatar de <?= htmlspecialchars($post['login']) ?>"
                            class="post-avatar me-2"
                            loading="lazy">
                    <div>
                        <div class="post-author">
                            <a
                                href="./pages/profil.php?id=<?= $post['user_id'] ?>"
                                class="text-decoration-none text-dark">
                                <?= htmlspecialchars($post['login']) ?>
                            </a>
                        </div>
                        <div class="post-time">
                            <?= isset($post['date']) ? date('d/m/Y H:i', strtotime($post['date'])) : 'Date inconnue' ?>
                        </div>
                    </div>
                </div>

                <div class="post-content">
                    <?php if (!empty($post['titre'])): ?>
                        <h4 class="mb-2"><?= htmlspecialchars($post['titre']) ?></h4>
                    <?php endif; ?>
                    
                    <?php if (!empty($post['base64_data'])): ?>
                        <!-- Afficher l'image depuis les données base64 -->
                        <img 
                            src="<?= htmlspecialchars($post['base64_data']) ?>"
                            class="post-image mb-3 rounded"
                            alt="Image publiée par <?= htmlspecialchars($post['login']) ?>"
                            loading="lazy">
                    <?php elseif (!empty($post['chemin'])): ?>
                        <!-- Afficher l'image depuis le chemin -->
                        <img 
                            src="<?= htmlspecialchars($post['chemin']) ?>"
                            class="post-image mb-3 rounded"
                            alt="Image publiée par <?= htmlspecialchars($post['login']) ?>"
                            onerror="this.onerror=null; this.src='avatar/avatar_1.png';"
                            loading="lazy">
                    <?php endif; ?>
                </div>

                <div class="post-actions d-flex justify-content-between">
                    <div class="post-action">
                        <i class="far fa-thumbs-up me-1"></i>
                        J'aime
                    </div>
                    <div
                        class="post-action"
                        onclick="window.location.href='commenter.php?id=<?= $post['id_pub'] ?>'">
                        <i class="far fa-comment-alt me-1"></i>
                        Commenter
                    </div>
                    <div class="post-action">
                        <i class="far fa-share-square me-1"></i>
                        Partager
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($publications)): ?>
            <div class="alert alert-info">
                Aucune publication à afficher. Soyez le premier à poster quelque chose !
            </div>
            <?php endif; ?>

            <div class="fil-section">
                <h2>Fil d'actualités</h2>
                <?php include 'pages/fil.php'; ?>
            </div>
        </div>

        <!-- Sidebar droite -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="sidebar">
                <!-- Suggestions d'amis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Suggestions d'amis</strong>
                    </div>
                    <div class="card-body p-2">
                        <?php foreach ($suggestions as $user): ?>
                        <div
                            class="friend-suggestion d-flex justify-content-between align-items-center mb-2">
                            <img
                                src="assets/images/default_avatar.png"
                                alt="Avatar de <?= htmlspecialchars($user['login']) ?>"
                                class="friend-avatar"
                                loading="lazy">
                            <div class="ms-2">
                                <a href="pages/profil.php?id=<?= $user['id_u'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($user['login']) ?>
                                </a>
                            </div>
                            <a
                                href="ajouter_ami.php?id=<?= $user['id_u'] ?>"
                                class="btn btn-primary btn-sm add-friend-btn">
                                <i class="fas fa-user-plus"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>

                        <?php if (empty($suggestions)): ?>
                        <div class="p-2">
                            <p class="text-muted">Aucune suggestion pour le moment</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tendances Gaming -->
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Tendances Gaming</strong>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">#GamingHub</li>
                            <li class="list-group-item">#MetaForg</li>
                            <li class="list-group-item">#JeuxVideo2025</li>
                            <li class="list-group-item">#CyberLeague</li>
                            <li class="list-group-item">#TournoisGaming</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/bnt-messagerie.php';?>
<?php include 'includes/footer.php'; ?>