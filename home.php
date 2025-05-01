<?php
ob_start();
session_start();
include "includes/connexion.php";

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: index.php");
    exit;
}

// Utilisation de la table images au lieu de publications puisque publications n'existe pas
// Cette modification temporaire permet d'éviter l'erreur
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
    // Si aucune image n'est disponible ou la table n'a pas les bonnes colonnes, créer des données fictives
    $publications = [];
}

// Récupération des suggestions d'utilisateurs (sans utiliser la table amis qui n'existe pas)
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
    // En cas d'erreur, créer un tableau vide
    $suggestions = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - MetaForg</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Styles personnalisés -->
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/home_style.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">
                <strong>MetaForg</strong>
            </a>
            
            <div class="d-flex align-items-center ms-auto me-2">
                <form class="d-flex me-2" action="pages/recherche.php" method="POST">
                    <div class="input-group">
                        <input type="search" class="form-control rounded-pill" placeholder="Rechercher sur MetaForg..." aria-label="Search" name="q">
                        <button class="btn btn-outline-primary rounded-pill ms-2" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="d-flex align-items-center">
                <a href="pages/profil.php?id=<?= $_SESSION['id_u'] ?>" class="text-decoration-none me-3">
                    <i class="fas fa-user"></i>
                </a>
                <a href="pages/amis.php" class="text-decoration-none me-3">
                    <i class="fas fa-user-friends"></i>
                </a>
                <a href="pages/messages.php" class="text-decoration-none me-3">
                    <i class="fas fa-envelope" style="color: #00FF00;"></i>
                </a>
                <a href="#" class="text-decoration-none me-3">
                    <i class="fas fa-bell"></i>
                </a>
                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none" href="#" role="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <strong class="me-1"><?= htmlspecialchars($_SESSION['login']) ?></strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
                        <li><a class="dropdown-item" href="pages/profil.php?id=<?= $_SESSION['id_u'] ?>">Mon profil</a></li>
                        <li><a class="dropdown-item" href="pages/compte.php">Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-5 pt-4">
        <div class="row">
            <!-- Sidebar gauche -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar">
                    <div class="list-group mb-4">
                        <a href="pages/profil.php?id=<?= $_SESSION['id_u'] ?>" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Mon profil
                        </a>
                        <a href="pages/amis.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-friends me-2"></i> Mes amis
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-gamepad me-2"></i> Jeux
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-users me-2"></i> Groupes
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-alt me-2"></i> Événements
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Fil d'actualité -->
            <div class="col-lg-6">
                <!-- Section Stories -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-2">
                        <div class="d-flex overflow-auto" style="gap: 15px; padding-bottom: 10px;">
                            <!-- Créer une story -->
                            <div class="text-center" style="min-width: 90px;">
                                <div class="position-relative">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; overflow: hidden;">
                                        <img src="assets/images/default_avatar.png" alt="Votre avatar" class="w-100 h-100" style="object-fit: cover;">
                                        <div class="position-absolute bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; bottom: 5px; right: 5px; border: 3px solid #3b5998;">
                                            <i class="fas fa-plus text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 small">Créer une story</div>
                            </div>
                            
                            <!-- Stories des amis (exemples) -->
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <div class="text-center" style="min-width: 90px;">
                                <div class="position-relative">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 90px; height: 90px; overflow: hidden; border: 3px solid #3b5998;">
                                        <img src="assets/images/default_avatar.png" alt="Avatar ami" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                </div>
                                <div class="mt-1 small">Ami <?php echo $i; ?></div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Créer une publication -->
                <div class="create-post mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            <img src="assets/images/default_avatar.png" alt="Avatar" class="post-avatar">
                        </div>
                        <input type="text" class="create-post-input" placeholder="Quoi de neuf, <?= htmlspecialchars($_SESSION['login']) ?>?" onclick="window.location.href='pages/poster.php'">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-secondary" onclick="window.location.href='pages/poster.php'">
                            <i class="fas fa-image me-1"></i> Photo
                        </button>
                        <button class="btn btn-outline-secondary" onclick="window.location.href='pages/poster.php'">
                            <i class="fas fa-video me-1"></i> Vidéo
                        </button>
                        <button class="btn btn-outline-secondary" onclick="window.location.href='pages/poster.php'">
                            <i class="fas fa-smile me-1"></i> Humeur
                        </button>
                    </div>
                </div>

                <!-- Publications -->
                <?php foreach ($publications as $post): ?>
                <div class="post-card">
                    <div class="post-header">
                        <img src="assets/images/default_avatar.png" alt="Avatar" class="post-avatar">
                        <div>
                            <div class="post-author">
                                <a href="pages/profil.php?id=<?= $post['user_id'] ?>" class="text-decoration-none text-dark">
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
                        <?php if (!empty($post['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" class="post-image mb-3" alt="Publication image">
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-actions">
                        <div class="post-action">
                            <i class="far fa-thumbs-up me-1"></i> J'aime
                        </div>
                        <div class="post-action" onclick="window.location.href='commenter.php?id=<?= $post['id'] ?? '' ?>'">
                            <i class="far fa-comment-alt me-1"></i> Commenter
                        </div>
                        <div class="post-action">
                            <i class="far fa-share-square me-1"></i> Partager
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($publications)): ?>
                <div class="alert alert-info">
                    Aucune publication à afficher. Soyez le premier à poster quelque chose !
                </div>
                <?php endif; ?>
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
                            <div class="friend-suggestion">
                                <img src="assets/images/default_avatar.png" alt="Avatar" class="friend-avatar">
                                <div>
                                    <a href="pages/profil.php?id=<?= $user['id_u'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($user['login']) ?>
                                    </a>
                                </div>
                                <a href="ajouter_ami.php?id=<?= $user['id_u'] ?>" class="btn btn-primary btn-sm add-friend-btn">
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
                    
                    <!-- Tendances -->
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
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Messagerie</strong>
                            <div>
                                <i class="fas fa-ellipsis-h"></i>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-2">
                                <input type="text" class="form-control form-control-sm" placeholder="Rechercher dans Messenger...">
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative">
                                            <img src="assets/images/default_avatar.png" alt="Avatar" class="rounded-circle" width="40">
                                            <span class="position-absolute bg-success rounded-circle" style="width: 10px; height: 10px; bottom: 3px; right: 3px; border: 2px solid white;"></span>
                                        </div>
                                        <div class="ms-2">
                                            <div class="fw-bold">Ami <?php echo $i; ?></div>
                                            <div class="small text-muted">En ligne</div>
                                        </div>
                                    </div>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center p-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="mb-0">© 2025 MetaForg - Tous droits réservés</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap & JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour gérer le clic sur la zone de création de post
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter des effets hover si nécessaire
            const postActions = document.querySelectorAll('.post-action');
            postActions.forEach(action => {
                action.addEventListener('mouseenter', () => {
                    action.style.backgroundColor = '#f0f2f5';
                });
                action.addEventListener('mouseleave', () => {
                    action.style.backgroundColor = 'transparent';
                });
            });
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
