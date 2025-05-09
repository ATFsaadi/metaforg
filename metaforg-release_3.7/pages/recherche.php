<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérification de session
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id_u'];
$search_results = [];

// Recherche d'utilisateurs (en utilisant GET)
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_term = '%' . $_GET['q'] . '%';

    // Recherche dans la base de données
    $req_search = $bdd->prepare("
        SELECT id_u, login
        FROM users
        WHERE login LIKE :search_term AND id_u != :user_id
        LIMIT 10
    ");
    $req_search->execute([
        'search_term' => $search_term,
        'user_id' => $user_id
    ]);
    $search_results = $req_search->fetchAll(PDO::FETCH_ASSOC);
}
?>
<div class="container mt-5 pt-4" id="reche-messag">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="search-container p-3">
                <!-- Formulaire de recherche -->
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Rechercher un utilisateur..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Affichage des résultats de la recherche -->
                <?php if (!empty($search_results)): ?>
                    <div class="search-results mt-3">
                        <h5>Résultats de la recherche:</h5>
                        <?php foreach ($search_results as $result): ?>
                            <div class="search-result-item p-2 border rounded mb-2" style="cursor:pointer" 
                            onclick="window.location.href='profil.php?id=<?= htmlspecialchars($result['id_u']) ?>'">
                                <div class="d-flex align-items-center">
                                    <img src="../assets/images/default_avatar.png" class="rounded-circle me-2" width="40" height="40">
                                    <div class="fw-bold"><?= htmlspecialchars($result['login']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (isset($_GET['q'])): ?>
                    <div class="text-center text-muted mt-3">
                        <p>Aucun utilisateur trouvé.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Menu déroulant de l'utilisateur -->
<div class="dropdown">
    <a class="dropdown-toggle text-decoration-none" href="#" role="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <strong class="me-1"><?= strtoupper(htmlspecialchars($_SESSION['login'])) ?></strong>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
        <li>
            <a class="dropdown-item" href="pages/profil.php?id=<?= $_SESSION['id_u'] ?>">Mon profil</a>
        </li>
        <li>
            <a class="dropdown-item" href="pages/compte.php">Paramètres</a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="logout.php">Déconnexion</a>
        </li>
    </ul>
</div>

<?php
ob_end_flush();
include '../includes/footer.php';
?>
