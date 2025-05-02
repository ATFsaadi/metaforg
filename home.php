<?php
ob_start();
session_start();
include "includes/header.php";
include "includes/connexion.php";

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: index.php");
    exit;
}

// Récupération des publications (images)
$req_posts = $bdd->prepare("
    SELECT i.id_img as id_pub, i.nom as titre, i.chemin as message, i.date_img as date, 
           u.login, u.id_u as user_id
    FROM images i
    JOIN users u ON i.u_id = u.id_u
    ORDER BY i.date_img DESC
    LIMIT 20
");

try {
    $req_posts->execute();
    $publications = $req_posts->fetchAll(PDO::FETCH_ASSOC);
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
                        href="pages/profil.php?id=<?= $_SESSION['id_u'] ?>"
                        class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i>
                        Mon profil
                    </a>
                    <a href="pages/amis.php" class="list-group-item list-group-item-action">
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
                                        src="assets/images/default_avatar.png"
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
                                        src="assets/images/default_avatar.png"
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
                            src="assets/images/default_avatar.png"
                            alt="Avatar"
                            class="post-avatar"
                            loading="lazy">
                    </div>
                    <input
                        type="text"
                        class="create-post-input form-control"
                        placeholder="Quoi de neuf, <?= htmlspecialchars($_SESSION['login']) ?>?"
                        onclick="window.location.href='pages/poster.php'">
                </div>
                <div class="d-flex justify-content-between">
                    <button
                        class="btn btn-outline-secondary"
                        onclick="window.location.href='pages/poster.php'">
                        <i class="fas fa-image me-1"></i>
                        Photo
                    </button>
                    <button
                        class="btn btn-outline-secondary"
                        onclick="window.location.href='pages/poster.php'">
                        <i class="fas fa-video me-1"></i>
                        Vidéo
                    </button>
                    <button
                        class="btn btn-outline-secondary"
                        onclick="window.location.href='pages/poster.php'">
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
                        src="assets/images/default_avatar.png"
                        alt="Avatar de <?= htmlspecialchars($post['login']) ?>"
                        class="post-avatar me-2"
                        loading="lazy">
                    <div>
                        <div class="post-author">
                            <a
                                href="pages/profil.php?id=<?= $post['user_id'] ?>"
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
                    <p><?= nl2br(htmlspecialchars($post['message'] ?? '')) ?></p>
                    <?php if (!empty($post['message'])): ?>
                    <img
                        src="<?= htmlspecialchars($post['message']) ?>"
                        class="post-image mb-3"
                        alt="Image publiée par <?= htmlspecialchars($post['login']) ?>"
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

                <!-- Messagerie -->
                <button
                    id="chatButton"
                    onclick="toggleChat()"
                    class="btn btn-primary position-fixed d-flex align-items-center"
                    style="bottom: 20px; left: 20px; z-index: 1050; padding: 10px 20px; border-radius: 8px;">
                    <i class="fas fa-comment-alt me-2"></i>
                    Messagerie
                </button>

                <div
                    id="chatPanel"
                    class="position-fixed bg-white shadow"
                    style="bottom: 70px; left: 20px; width: 300px; max-height: 400px; overflow-y: auto; display: none; z-index: 1040; border-radius: 10px;">
                    <div
                        class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Messagerie</h6>
                        <button class="btn btn-sm btn-secondary" onclick="toggleChat()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="d-flex align-items-center p-2 border-bottom">
                            <div class="position-relative">
                                <img
                                    src="assets/images/default_avatar.png"
                                    alt="Avatar Ami <?= $i ?>"
                                    class="rounded-circle"
                                    width="40"
                                    loading="lazy">
                                <span
                                    class="position-absolute bg-success rounded-circle"
                                    style="width: 10px; height: 10px; bottom: 3px; right: 3px; border: 2px solid white;"></span>
                            </div>
                            <div class="ms-2">
                                <div class="fw-bold">Ami
                                    <?= $i ?></div>
                                <div class="small text-muted">En ligne</div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleChat() {
        const panel = document.getElementById('chatPanel');
        const isVisible = panel.style.display !== 'none';
        panel.style.display = isVisible
            ? 'none'
            : 'block';
        if (!isVisible) {
            panel
                .classList
                .add('slideIn');
            setTimeout(() => panel.classList.remove('slideIn'), 300);
        }
    }

    document.addEventListener('click', function (event) {
        const chatPanel = document.getElementById('chatPanel');
        const chatButton = document.getElementById('chatButton');
        if (!chatPanel.contains(event.target) && !chatButton.contains(event.target)) {
            chatPanel.style.display = 'none';
        }
    });
</script>

<?php include 'includes/footer.php'; ?>