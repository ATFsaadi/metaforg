<?php
/**
 * Recherche unifiée - Combine les fonctionnalités de recherche.php et search.php
 * Ce fichier permet de rechercher à la fois des utilisateurs et du contenu
 */
session_start();

// Inclusions communes
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_u'])) {
    header("Location: ../index.php");
    exit;
}

// Initialisation des variables
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'all'; // Type de recherche: all, users, posts
$results = [];
$userResults = [];
$postResults = [];

// Traitement de la recherche si un terme est fourni
if (!empty($searchTerm)) {
    // 1. Recherche d'utilisateurs
    $userQuery = $bdd->prepare("
        SELECT id_u, login, email, lvl
        FROM users 
        WHERE login LIKE :term OR email LIKE :term
        LIMIT 10
    ");
    $userQuery->execute(['term' => "%$searchTerm%"]);
    $userResults = $userQuery->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Recherche de publications
    if ($type === 'all' || $type === 'posts') {
        $postQuery = $bdd->prepare("
            SELECT i.id_img, i.nom, i.date_img, u.login, u.id_u
            FROM images i
            JOIN users u ON i.u_id = u.id_u
            WHERE i.nom LIKE :term
            ORDER BY i.date_img DESC
            LIMIT 20
        ");
        $postQuery->execute(['term' => "%$searchTerm%"]);
        $postResults = $postQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<div class="container mt-5 pt-3">
    <div class="card shadow">
        <div class="card-header bg-dark text-light">
            <h2 class="mb-0"><i class="fas fa-search me-2"></i> Recherche</h2>
        </div>
        
        <div class="card-body">
            <!-- Formulaire de recherche -->
            <form method="GET" action="" class="mb-4">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="q" 
                        class="form-control" 
                        placeholder="Rechercher des utilisateurs ou du contenu..."
                        value="<?= htmlspecialchars($searchTerm) ?>"
                        required
                    >
                    <select name="type" class="form-select" style="max-width: 180px;">
                        <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Tout</option>
                        <option value="users" <?= $type === 'users' ? 'selected' : '' ?>>Utilisateurs</option>
                        <option value="posts" <?= $type === 'posts' ? 'selected' : '' ?>>Publications</option>
                    </select>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
            
            <?php if (empty($searchTerm)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Entrez un terme de recherche pour trouver des utilisateurs ou du contenu.
                </div>
            <?php elseif (empty($userResults) && empty($postResults)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i> Aucun résultat trouvé pour <strong>"<?= htmlspecialchars($searchTerm) ?>"</strong>
                </div>
            <?php else: ?>
                <!-- Affichage des résultats -->
                
                <!-- Résultats utilisateurs -->
                <?php if (!empty($userResults) && ($type === 'all' || $type === 'users')): ?>
                    <h3 class="mt-4 mb-3" style="color: #00FF00;">
                        <i class="fas fa-users me-2"></i> Utilisateurs
                    </h3>
                    <div class="list-group mb-4">
                        <?php foreach ($userResults as $user): ?>
                            <a href="profil.php?id=<?= $user['id_u'] ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <img 
                                    src="../avatar.php?id=<?= $user['id_u'] ?>" 
                                    class="rounded-circle me-3" 
                                    width="40" 
                                    height="40" 
                                    alt="Avatar de <?= htmlspecialchars($user['login']) ?>"
                                >
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($user['login']) ?></h5>
                                    <?php if ($user['id_u'] !== $_SESSION['id_u']): ?>
                                        <button 
                                            class="btn btn-sm btn-outline-success add-friend-btn" 
                                            data-user-id="<?= $user['id_u'] ?>"
                                        >
                                            <i class="fas fa-user-plus"></i> Ajouter
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Résultats publications -->
                <?php if (!empty($postResults) && ($type === 'all' || $type === 'posts')): ?>
                    <h3 class="mt-4 mb-3" style="color: #00FF00;">
                        <i class="fas fa-images me-2"></i> Publications
                    </h3>
                    <div class="row">
                        <?php foreach ($postResults as $post): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-dark text-light d-flex align-items-center">
                                        <img 
                                            src="../avatar.php?id=<?= $post['id_u'] ?>" 
                                            class="rounded-circle me-2" 
                                            width="30" 
                                            height="30" 
                                            alt="Avatar"
                                        >
                                        <a href="profil.php?id=<?= $post['id_u'] ?>" class="text-light text-decoration-none">
                                            <?= htmlspecialchars($post['login']) ?>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($post['nom']) ?></h5>
                                        <p class="card-text text-muted">
                                            <small><i class="far fa-clock me-1"></i> <?= date('d/m/Y H:i', strtotime($post['date_img'])) ?></small>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-light border-0">
                                        <a href="../home.php?post=<?= $post['id_img'] ?>" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Script pour ajouter un ami en AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons d'ajout d'amis
    const addFriendButtons = document.querySelectorAll('.add-friend-btn');
    
    addFriendButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            
            // Envoyer une requête AJAX pour ajouter l'ami
            fetch('../pages/add_friend.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'friend_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Changer le bouton pour indiquer que l'ami a été ajouté
                    this.innerHTML = '<i class="fas fa-check"></i> Ajouté';
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                    this.disabled = true;
                } else {
                    alert(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'ajout de l\'ami');
            });
        });
    });
});
</script>

<?php include "../includes/footer.php"; ?>
