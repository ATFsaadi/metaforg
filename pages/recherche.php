<?php
session_start();

include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifier que l'utilisateur est connecté
/* if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
} */

// Traitement de la recherche test
$searchTerm = '';
$results = [];

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $searchTerm = trim($_GET['q']);
    
    $stmt = $bdd->prepare("SELECT id_u, login, avatar FROM users WHERE login LIKE ? AND id_u != ?");
    $stmt->execute(["%$searchTerm%", $_SESSION['user']['id']]);
    $results = $stmt->fetchAll();
}
?>



    <h2>Rechercher des amis</h2>
    
    <form method="GET" class="search-form">
        <input 
            type="text" 
            name="q" 
            value="<?= htmlspecialchars($searchTerm) ?>" 
            placeholder="Entrez un pseudo..."
            required
        >
        <button type="submit">🔍</button>
    </form>

    <div class="results-container">
        <?php if (!empty($searchTerm)): ?>
            <?php if (empty($results)): ?>
                <p>Aucun résultat trouvé pour "<?= htmlspecialchars($searchTerm) ?>"</p>
            <?php else: ?>
                <?php foreach ($results as $user): ?>
                    <div class="user-card">
                        <img 
                            src="uploads/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.jpg') ?>" 
                            class="user-avatar" 
                            alt="Avatar de <?= htmlspecialchars($user['login']) ?>"
                        >
                        <span><?= htmlspecialchars($user['login']) ?></span>
                        
                        <?php
                        // Vérifier si déjà ami/en attente
                        $check = $bdd->prepare("SELECT statut FROM amis WHERE 
                            (utilisateur_id = ? AND ami_id = ?) OR 
                            (utilisateur_id = ? AND ami_id = ?)");
                        $check->execute([
                            $_SESSION['user']['id'], $user['id_u'],
                            $user['id_u'], $_SESSION['user']['id']
                        ]);
                        $relation = $check->fetch();
                        ?>
                        
                        <?php if (!$relation): ?>
                            <a 
                                href="ajouter_ami.php?id=<?= $user['id_u'] ?>&csrf=<?= $_SESSION['csrf_token'] ?>" 
                                class="add-btn"
                            >
                                Ajouter
                            </a>
                        <?php elseif ($relation['statut'] == 'en_attente'): ?>
                            <span class="pending-badge">En attente</span>
                        <?php elseif ($relation['statut'] == 'accepte'): ?>
                            <span class="friend-badge">✔ Déjà ami</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
