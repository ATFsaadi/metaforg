<?php
ob_start();
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérification de session
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id_u'];
$search_results = [];

// Recherche d'utilisateurs
if (isset($_POST['search_user'])) {
    $search_term = '%' . $_POST['search_term'] . '%';
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
<title>Recherche - MetaForg</title>
<div class="container mt-5 pt-4" id="reche-messag">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="search-container p-3">
                <!-- Formulaire de recherche -->
                <form method="POST" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search_term" class="form-control" placeholder="Rechercher un utilisateur..." required>
                        <button type="submit" name="search_user" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <?php if (!empty($search_results)): ?>
                    <div class="search-results mt-3">
                        <h5>Résultats de la recherche:</h5>
                        <?php foreach ($search_results as $result): ?>
    <div class="search-result-item p-2 border rounded mb-2">
        <div class="d-flex align-items-center justify-content-between">
            <div onclick="window.location.href='profil.php?id=<?= htmlspecialchars($result['id_u']) ?>'" style="cursor:pointer;">
<img
    src="../assets/images/default_avatar.png"
    class="rounded-circle me-2"
    width="40"
    height="40">
<span class="fw-bold"><?= htmlspecialchars($result['login']) ?></span>
</div>
<a
href="ajouter_ami.php?id=<?= $result['id_u'] ?>&csrf=<?= $_SESSION['csrf_token'] ?>"
class="btn btn-sm btn-success">
Ajouter
</a>
</div>
</div>
<?php endforeach; ?>

</div>
<?php elseif (isset($_POST['search_user'])): ?>
<div class="text-center text-muted mt-3">
<p>Aucun utilisateur trouvé.</p>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>

<!-- Scripts -->
<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
ob_end_flush();
include '../includes/footer.php';
?>