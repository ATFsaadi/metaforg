<?php
ob_start();
session_start();
include "includes/header.php";

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['id_u'] ?? null;
$login = $_SESSION['login'] ?? 'utilisateur';

// --- Ajout d'ami ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_friend' && isset($_POST['ami_id'])) {
    $ami_id = (int) $_POST['ami_id'];

    if ($userId === $ami_id) {
        $_SESSION['friend_message'] = ['type' => 'danger', 'text' => 'Vous ne pouvez pas vous ajouter vous-même'];
    } else {
        $stmt = $bdd->prepare("SELECT * FROM amis 
            WHERE (utilisateur_id = ? AND ami_id = ?) 
               OR (utilisateur_id = ? AND ami_id = ?)");
        $stmt->execute([$userId, $ami_id, $ami_id, $userId]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['friend_message'] = ['type' => 'warning', 'text' => 'Une demande existe déjà'];
        } else {
            $insert = $bdd->prepare("INSERT INTO amis (utilisateur_id, ami_id, statut) 
                                     VALUES (?, ?, 'en_attente')");
            $insert->execute([$userId, $ami_id]);
            $_SESSION['friend_message'] = ['type' => 'success', 'text' => 'Demande envoyée avec succès'];
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Chargement des publications ---
try {
    $req_posts = $bdd->prepare("
        SELECT i.id_img AS id_pub, i.nom AS titre, i.chemin AS chemin_image, i.date_img AS date, 
               u.login, u.id_u AS user_id, i.type_img AS type
        FROM images i
        JOIN users u ON i.u_id = u.id_u
        ORDER BY i.date_img DESC
        LIMIT 20
    ");
    $req_posts->execute();
    $publications = $req_posts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $publications = [];
}

// --- Chargement des suggestions d'amis ---
try {
    $req_users = $bdd->prepare("
        SELECT u.id_u, u.login 
        FROM users u
        WHERE u.id_u != :id
          AND u.id_u NOT IN (
              SELECT ami_id FROM amis WHERE utilisateur_id = :id
              UNION
              SELECT utilisateur_id FROM amis WHERE ami_id = :id
          )
        ORDER BY RAND()
        LIMIT 8
    ");
    $req_users->bindValue(':id', $userId, PDO::PARAM_INT);
    $req_users->execute();
    $suggestions = $req_users->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $suggestions = [];
}


// --- Chargement des stories ---
try {
    $req_stories = $bdd->prepare("
        SELECT s.*, u.login 
        FROM stories s
        JOIN users u ON s.user_id = u.id_u
        WHERE s.expire_at > NOW()
        ORDER BY s.created_at DESC
    ");
    $req_stories->execute();
    $stories = $req_stories->fetchAll(PDO::FETCH_ASSOC);
    
  
} catch (PDOException $e) {
    $stories = [];
}

?>

<div class="container mt-5 pt-4">
  <div class="row">

    <!-- Sidebar gauche -->
    <div class="col-lg-3 d-none d-lg-block">
      <div class="sidebar">
        <div class="list-group mb-4">
          <a href="pages/profil.php?id=<?= $userId ?>" class="list-group-item list-group-item-action">
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
        <div id="rss-news">
          <div class="news-source">
            <h4>ActuGaming</h4>
            <div id="actugaming-list" class="news-list"></div>
          </div>
          <div class="news-source">
            <h4>JVFrance</h4>
            <div id="jvfrance-list" class="news-list"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Fil d'actualité -->
    <div class="col-lg-6">
<!-- Conteneur principal des stories -->
<div class="stories-wrapper d-flex align-items-center mb-4 position-relative">

  <button class="scroll-btn left" style="display: none;">
    <i class="fas fa-chevron-left"></i>
  </button>

  <div class="stories-container story-container d-flex flex-nowrap overflow-auto flex-grow-1 gap-3">
    
    <!-- Créer une story -->
    <div class="story-item create-story" onclick="window.location.href='pages/ajouter_story.php'">
      <div class="story-avatar-wrapper">
        <img src="assets/images/profil/profil_<?= $userId ?>" 
             alt="Votre avatar" 
             class="story-avatar"
             loading="lazy"
             onerror="this.src='assets/images/profil/default.png'">
        <div class="story-plus-icon bg-primary">
          <i class="fas fa-plus text-white"></i>
        </div>
      </div>
      <div class="story-username">Créer une story</div>
    </div>

    <!-- Stories dynamiques -->
    <?php foreach ($stories as $story): ?>
      <div class="story-item" 
           onclick="openStoryViewer(<?= $story['id'] ?>)"
           title="Story de <?= htmlspecialchars($story['login']) ?>">
        <div class="story-avatar-wrapper viewed-story">
          <img src="assets/images/story/<?= htmlspecialchars($story['image_path']) ?>" 
               alt="Story de <?= htmlspecialchars($story['login']) ?>" 
               class="story-avatar"
               loading="lazy">
          <div class="story-progress" style="width: 0%"></div>
        </div>
        <div class="story-username"><?= htmlspecialchars($story['login']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>

  <button class="scroll-btn right">
    <i class="fas fa-chevron-right"></i>
  </button>
</div>

      <!-- Créer une publication -->
      <div class="create-post mb-4 mt-3">
        <div class="d-flex align-items-center mb-3">
          <div class="me-2">
            <img src="assets/images/profil/profil_<?= $userId ?>" alt="Avatar" class="post-avatar" loading="lazy" onerror="this.src='assets/images/profil/default.png'">
          </div>
          <input type="text" class="create-post-input form-control" placeholder="Quoi de neuf, <?= htmlspecialchars($login) ?> ?" onclick="window.location.href='pages/poster.php'">
        </div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="d-flex justify-content-center">
  <button class="btn btn-outline-secondary" onclick="window.location.href='pages/poster.php'">
    <i class="fas fa-upload me-1"></i> Publie
  </button>
</div>

      </div>

      <!-- Publications -->
      <?php if (!empty($publications)): ?>
        <?php foreach ($publications as $post): ?>
          <div class="post-card mb-4">
            <div class="post-header d-flex align-items-center">
              <img src="assets/images/profil/profil_<?= $post['user_id'] ?>" alt="Avatar de <?= htmlspecialchars($post['login']) ?>" class="post-avatar me-2" loading="lazy" onerror="this.src='assets/images/profil/default.png'">
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
              <p><?= nl2br(htmlspecialchars($post['titre'] ?? '')) ?></p>
              <?php if (!empty($post['chemin_image'])): ?>
                <img src="<?= htmlspecialchars($post['chemin_image']) ?>" class="post-image mb-3" alt="Image de publication" loading="lazy">
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
      <?php else: ?>
       
      <?php endif; ?>

      <div class="fil-section mt-4">
    
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
            if (isset($_SESSION['friend_message'])) {
                echo '<div class="alert alert-'.htmlspecialchars($_SESSION['friend_message']['type']).' mb-3">'
                    .htmlspecialchars($_SESSION['friend_message']['text']).
                    '</div>';
                unset($_SESSION['friend_message']);
            }

            foreach ($suggestions as $user):
                $demande_existe = false;
                if ($userId) {
                    $check_demande = $bdd->prepare("SELECT * FROM amis 
                        WHERE (utilisateur_id = ? AND ami_id = ?) 
                        OR (utilisateur_id = ? AND ami_id = ?)");
                    $check_demande->execute([$userId, $user['id_u'], $user['id_u'], $userId]);
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

              <?php if (!$demande_existe): ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="add_friend">
                  <input type="hidden" name="ami_id" value="<?= $user['id_u'] ?>">
                  <button type="submit" class="btn btn-primary btn-sm" title="Ajouter comme ami">
                    <i class="fas fa-user-plus"></i>
                  </button>
                </form>
              <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled title="Demande en attente">
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
document.addEventListener('DOMContentLoaded', function() {
  const container = document.querySelector('.story-container');
  const btnLeft = document.querySelector('.scroll-btn.left');
  const btnRight = document.querySelector('.scroll-btn.right');
  
  // Variables pour le défilement automatique
  let scrollInterval;
  let autoScrollTimeout;
  const scrollSpeed = 3; // Vitesse de défilement au survol
  const scrollStep = 200; // Distance de défilement au clic
  const autoScrollDelay = 3000; // Délai entre chaque défilement automatique (3s)

  // Fonction pour mettre à jour la visibilité des flèches
  function updateScrollButtons() {
    const maxScrollLeft = container.scrollWidth - container.clientWidth;
    const showLeft = container.scrollLeft > 0;
    const showRight = container.scrollLeft < maxScrollLeft - 5; // Marge d'erreur
    
    btnLeft.style.display = showLeft ? 'flex' : 'none';
    btnRight.style.display = showRight ? 'flex' : 'none';
    
    // Si on est tout à droite, réinitialiser après un délai
    if (!showRight) {
      setTimeout(() => {
        container.scrollTo({ left: 0, behavior: 'smooth' });
      }, 2000);
    }
  }

  // Défilement continu au survol
  function startAutoScroll(direction) {
    stopAutoScroll();
    scrollInterval = setInterval(() => {
      container.scrollLeft += direction === 'right' ? scrollSpeed : -scrollSpeed;
      updateScrollButtons();
    }, 20);
  }

  function stopAutoScroll() {
    clearInterval(scrollInterval);
  }

  // Défilement automatique périodique
  function setupAutoScroll() {
    clearTimeout(autoScrollTimeout);
    autoScrollTimeout = setTimeout(() => {
      const maxScrollLeft = container.scrollWidth - container.clientWidth;
      
      if (container.scrollLeft >= maxScrollLeft - 10) {
        container.scrollTo({ left: 0, behavior: 'smooth' });
      } else {
        container.scrollBy({ left: scrollStep, behavior: 'smooth' });
      }
      
      setupAutoScroll();
    }, autoScrollDelay);
  }

  // Événements pour les boutons
  btnLeft.addEventListener('click', (e) => {
    e.preventDefault();
    container.scrollBy({ left: -scrollStep, behavior: 'smooth' });
  });
  
  btnRight.addEventListener('click', (e) => {
    e.preventDefault();
    container.scrollBy({ left: scrollStep, behavior: 'smooth' });
  });

  // Survol des boutons
  btnLeft.addEventListener('mouseenter', () => startAutoScroll('left'));
  btnLeft.addEventListener('mouseleave', stopAutoScroll);
  btnRight.addEventListener('mouseenter', () => startAutoScroll('right'));
  btnRight.addEventListener('mouseleave', stopAutoScroll);

  // Écouteurs globaux
  container.addEventListener('scroll', updateScrollButtons);
  window.addEventListener('resize', updateScrollButtons);
  
  // Initialisation
  updateScrollButtons();
  setupAutoScroll();
  
  // Pause auto-scroll quand l'utilisateur interagit
  container.addEventListener('mousedown', () => clearTimeout(autoScrollTimeout));
  container.addEventListener('touchstart', () => clearTimeout(autoScrollTimeout));
  container.addEventListener('wheel', () => clearTimeout(autoScrollTimeout));
});
</script>

<?php include "includes/mini-messagerie.php"; ?>
<?php include "includes/footer.php"; ?>


