<?php 
session_start();
include "../includes/connexion.php";

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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'amis</title>
    <style>
        .user-card {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        .add-btn {
            margin-left: auto;
            padding: 5px 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .pending-badge,
        .friend-badge {
            margin-left: auto;
            font-weight: bold;
            color: #888;
        }
    </style>
</head>
<body>

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

</body>
</html>
