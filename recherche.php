<?php
session_start();
include "includes/connexion.php";

// Vérification connexion
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Nettoyage de la recherche
$searchTerm = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'])) : '';

// Requête sécurisée
$query = $bdd->prepare("
    SELECT id_u, login, avatar 
    FROM users 
    WHERE 
        login LIKE :search AND
        id_u != :current_id AND
        id_u NOT IN (
            SELECT ami_id FROM amis WHERE user_id = :current_id AND status = 'blocked'
        )
    ORDER BY 
        CASE 
            WHEN login = :exact_match THEN 0 
            WHEN login LIKE :start_match THEN 1 
            ELSE 2 
        END
    LIMIT 15
");

$query->execute([
    'search' => "%$searchTerm%",
    'current_id' => $_SESSION['user']['id'],
    'exact_match' => $searchTerm,
    'start_match' => "$searchTerm%"
]);

$results = $query->fetchAll();
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
                        $check = $bdd->prepare("SELECT status FROM amis WHERE 
                            (user_id = ? AND ami_id = ?) OR 
                            (user_id = ? AND ami_id = ?)");
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
                        <?php elseif ($relation['status'] == 'pending'): ?>
                            <span class="pending-badge">En attente</span>
                        <?php elseif ($relation['status'] == 'accepted'): ?>
                            <span class="friend-badge">✔ Déjà ami</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>