<?php
ob_start();
session_start();
include "includes/header.php";
// Traitement de l'ajout d'ami
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_friend') {
    if (isset($_SESSION['id_u'], $_POST['ami_id'])) {
        $utilisateur_id = (int)$_SESSION['id_u'];
        $ami_id = (int)$_POST['ami_id'];

        try {
            // Empêcher de s'ajouter soi-même
            if ($utilisateur_id === $ami_id) {
                $_SESSION['friend_message'] = [
                    'type' => 'danger',
                    'text' => 'Vous ne pouvez pas vous ajouter vous-même'
                ];
            } else {
                // Vérifier si la demande existe déjà
                $check = $bdd->prepare("SELECT * FROM amis 
                                      WHERE (utilisateur_id = ? AND ami_id = ?) 
                                      OR (utilisateur_id = ? AND ami_id = ?)");
                $check->execute([$utilisateur_id, $ami_id, $ami_id, $utilisateur_id]);

                if ($check->rowCount() > 0) {
                    $_SESSION['friend_message'] = [
                        'type' => 'warning',
                        'text' => 'Une demande existe déjà'
                    ];
                } else {
                    // Envoyer la demande
                    $stmt = $bdd->prepare("INSERT INTO amis (utilisateur_id, ami_id, statut) 
                                          VALUES (?, ?, 'en_attente')");
                    $stmt->execute([$utilisateur_id, $ami_id]);
                    
                    $_SESSION['friend_message'] = [
                        'type' => 'success',
                        'text' => 'Demande envoyée avec succès'
                    ];
                }
            }
        } catch (PDOException $e) {
            $_SESSION['friend_message'] = [
                'type' => 'danger',
                'text' => 'Erreur: ' . $e->getMessage()
            ];
        }
        
        // Recharger la page pour afficher le message
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
}

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: index.php");
    exit;
}

// Récupération des publications (images)
$req_posts = $bdd->prepare("
    SELECT i.id_img as id_pub, i.nom as titre, i.chemin as message, i.date_img as date, 
           u.login, u.id_u as user_id, i.type_img as type
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

/* Suggestions d'utilisateurs de sorte à ne pas afficher notre propre profil 
 et afficher la suggestion d'une façon aléatoire */
try {
    $req_users = $bdd->prepare("
        SELECT u.id_u, u.login 
        FROM users u
        WHERE u.id_u != :user_id
        ORDER BY RAND()
        LIMIT 8
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
          <a href="pages/profil.php?id=<?= $_SESSION['id_u'] ?>" class="list-group-item list-group-item-action">
            <i class="fas fa-user me-2"></i> Mon profil
          </a>
          <a href="pages/amis.php" class="list-group-item list-group-item-action">
            <i class="fas fa-user-friends me-2"></i> Mes amis
          </a>
          <a href="pages/jeux.php" class="list-group-item list-group-item-action">
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
      <!-- Stories avec scroll -->
      <div class="position-relative px-4">
        <!-- Bouton de défilement gauche -->
        <button class="scroll-btn left btn btn-sm position-absolute top-50 start-0 translate-middle-y" style="z-index: 2;" aria-label="Faire défiler les stories vers la gauche">◀</button>

        <!-- Conteneur des stories -->
        <div class="story-container">
          <!-- Créer une story -->
          <div class="story-create">
            <div class="story-avatar-wrapper">
              <img src="assets/images/meta.png" alt="Votre avatar" loading="lazy">
              <div class="story-plus-icon"><i class="fas fa-plus"></i></div>
            </div>
            <div class="mt-1 small">Créer une story</div>
          </div>

          <!-- Stories dynamiques -->
          <?php for ($i = 1; $i <= 10; $i++): ?>
          <div class="story">
            <div class="story-avatar-wrapper avatar-border">
              <img src="assets/images/story/story_<?= $i ?>" alt="Story ami <?= $i ?>" loading="lazy">
            </div>
            <div class="mt-1 small">Story <?= $i ?></div>
          </div>
          <?php endfor; ?>
        </div>

        <!-- Bouton de défilement droit -->
        <button class="scroll-btn right btn btn-sm position-absolute top-50 end-0 translate-middle-y" style="z-index: 2;" aria-label="Faire défiler les stories vers la droite">▶</button>
      </div>

      <!-- Créer une publication -->
      <div class="create-post mb-4 mt-3">
        <div class="d-flex align-items-center mb-3">
          <div class="me-2">
          <img src="assets/images/profil/profil_<?= $_SESSION['id_u'] ?>" alt="Avatar" class="post-avatar" loading="lazy">
          </div>
          <input type="text" class="create-post-input form-control" placeholder="Quoi de neuf, <?= htmlspecialchars($_SESSION['login'] ?? 'utilisateur') ?> ?" onclick="window.location.href='pages/poster.php'">
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
      <div class="post-card mb-4">
        <div class="post-header d-flex align-items-center">
          <img src="assets/images/default_avatar.png"
               alt="Avatar de <?= htmlspecialchars($post['login']) ?>"
               class="post-avatar me-2" loading="lazy">
          <div>
            <div class="post-author">
              <a href="pages/profil.php?id=<?= $post['user_id'] ?>" class="text-decoration-none text-light">
                <?= htmlspecialchars($post['login']) ?>
              </a>
            </div>
            <div class="post-time">
              <?= isset($post['date']) ? date('d/m/Y H:i', strtotime($post['date'])) : 'Date inconnue' ?>
            </div>
          </div>
        </div>

        <div class="post-content mt-2">
          <p><?= nl2br(htmlspecialchars($post['message'] ?? '')) ?></p>
          <?php if (!empty($post['message'])): ?>
            <img src="<?= htmlspecialchars($post['message']) ?>" class="post-image mb-3" alt="Image de publication" loading="lazy">
          <?php endif; ?>
        </div>

        <div class="post-actions d-flex justify-content-between">
          <div class="post-action"><i class="far fa-thumbs-up me-1"></i> J'aime</div>
          <div class="post-action" onclick="window.location.href='commenter.php?id=<?= $post['id_pub'] ?>'">
            <i class="far fa-comment-alt me-1"></i> Commenter
          </div>
          <div class="post-action"><i class="far fa-share-square me-1"></i> Partager</div>
        </div>
      </div>
      <?php endforeach; ?>

      <!--?php if (empty($publications)): ?>
        <div class="alert alert-info">Aucune publication à afficher. Soyez le premier à poster !</div>
      ?php endif; ?>-->

      <div class="fil-section mt-4">
        <h2 class="text-light">Fil d'actualités</h2>
        <?php include 'pages/fil.php'; ?>
      </div>
    </div>

    <!-- Sidebar droite -->
    <div class="col-lg-3 d-none d-lg-block">
      <div class="sidebar">
     <!-- Suggestions d'amis -->
<div class="card mb-4">
    <div class="card-header"><strong>Suggestions d'amis</strong></div>
    <div class="card-body p-2">
        <?php 
        // Afficher le message de confirmation si présent
        if (isset($_SESSION['friend_message'])) {
            echo '<div class="alert alert-'.$_SESSION['friend_message']['type'].' mb-3">'
                .$_SESSION['friend_message']['text'].
                '</div>';
            unset($_SESSION['friend_message']);
        }
        
        foreach ($suggestions as $user): 
            // Vérifier si une demande existe déjà (nouveau code)
            $demande_existe = false;
            if (isset($_SESSION['id_u'])) {
                $check_demande = $bdd->prepare("SELECT * FROM amis 
                    WHERE (utilisateur_id = ? AND ami_id = ?) 
                    OR (utilisateur_id = ? AND ami_id = ?)");
                $check_demande->execute([$_SESSION['id_u'], $user['id_u'], $user['id_u'], $_SESSION['id_u']]);
                $demande_existe = $check_demande->rowCount() > 0;
            }
        ?>
        <div class="friend-suggestion d-flex align-items-center mb-2">
           <img src="assets/images/profil/profil_<?= $user['id_u'] ?>" 
     alt="Avatar de <?= htmlspecialchars($user['login']) ?>" 
     class="friend-avatar" loading="lazy"
     onerror="this.src='assets/images/profil/default.png';">

            <div class="ms-2 flex-grow-1 sugg">
                <a href="pages/profil.php?id=<?= $user['id_u'] ?>" class="text-decoration-none">
                    <?= htmlspecialchars($user['login']) ?>
                </a>
            </div>
            <!-- Formulaire pour ajouter un ami - Version améliorée -->
            <?php if (!$demande_existe): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="add_friend">
                    <input type="hidden" name="ami_id" value="<?= $user['id_u'] ?>">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled>
                    <i class="fas fa-clock"></i>
                </button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if (empty($suggestions)): ?>
            <div class="p-2 text-muted">Aucune suggestion pour le moment</div>
        <?php endif; ?>
    </div>
</div>
        <!-- Tendances -->
        <div class="card mb-4">
          <div class="card-header"><strong>Tendances Gaming</strong></div>
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


<!-- Scroll JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.story-container');
    const btnLeft = document.querySelector('.scroll-btn.left');
    const btnRight = document.querySelector('.scroll-btn.right');

    let scrollInterval;
    let isScrollingManually = false;

    // Fonction de défilement automatique
    function startAutoScroll(direction) {
        if (isScrollingManually) return;

        const step = 10;
        scrollInterval = setInterval(() => {
            container.scrollLeft += direction === 'right' ? step : -step;
            updateScrollButtons();
        }, 50);
    }

    function stopAutoScroll() {
        clearInterval(scrollInterval);
    }

    // Fonction de mise à jour des boutons de défilement
    function updateScrollButtons() {
        const maxScrollLeft = container.scrollWidth - container.clientWidth;
        btnLeft.style.display = container.scrollLeft > 0 ? 'inline-block' : 'none';
        btnRight.style.display = container.scrollLeft < maxScrollLeft ? 'inline-block' : 'none';
    }

    // Gestion des événements de survol pour démarrer et arrêter le défilement automatique
    btnLeft.addEventListener('mouseenter', () => startAutoScroll('left'));
    btnLeft.addEventListener('mouseleave', stopAutoScroll);
    btnRight.addEventListener('mouseenter', () => startAutoScroll('right'));
    btnRight.addEventListener('mouseleave', stopAutoScroll);

    // Défilement manuel avec les boutons
    btnLeft.addEventListener('click', () => {
        isScrollingManually = true;
        container.scrollBy({ left: -150, behavior: 'smooth' });
        setTimeout(() => isScrollingManually = false, 500);
    });

    btnRight.addEventListener('click', () => {
        isScrollingManually = true;
        container.scrollBy({ left: 150, behavior: 'smooth' });
        setTimeout(() => isScrollingManually = false, 500);
    });

    // Défilement automatique toutes les 3 secondes
    setInterval(() => {
        if (isScrollingManually) return;

        const maxScrollLeft = container.scrollWidth - container.clientWidth;
        const currentScrollLeft = container.scrollLeft;
        const step = 150;

        if (currentScrollLeft + container.clientWidth < maxScrollLeft) {
            container.scrollBy({ left: step, behavior: 'smooth' });
        } else {
            container.scrollTo({ left: 0, behavior: 'smooth' });
        }
    }, 3000);

    // Mettre à jour l'état des boutons de défilement
    container.addEventListener('scroll', updateScrollButtons);
    window.addEventListener('resize', updateScrollButtons);
    window.addEventListener('load', updateScrollButtons);
});

</script>




<?php
include 'includes/mini-messagerie.php';
include 'includes/footer.php'; ?>

