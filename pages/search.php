<?php
require_once "../includes/connexion.php";
$title = "Recherche d'utilisateurs";

// Définir l'ID de l'utilisateur connecté s'il existe
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Traitement de la recherche
$resultats = [];
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = $_GET['q'];
    $resultats = rechercherUtilisateurs($bdd, $query);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Metaforge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <style>
        .search-title {
            color: #00FF00;
            margin-bottom: 20px;
        }
        .search-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-form {
            margin-bottom: 30px;
        }
        .friend-button {
            background-color: #00FF00;
            border-color: #00FF00;
            color: #000;
        }
        .friend-button:hover {
            background-color: #00CC00;
            border-color: #00CC00;
            color: #000;
        }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container search-container">
        <h1 class="search-title">Recherche d'utilisateurs</h1>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="search-form">
            <?php afficherFormulaireRecherche('', 'GET'); ?>
        </div>

        <?php if (isset($_GET['q']) && !empty($_GET['q'])): ?>
            <div class="search-results">
                <h2 class="search-title">Résultats pour "<?php echo htmlspecialchars($_GET['q']); ?>"</h2>
                <?php afficherResultatsRecherche($resultats, $user_id); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($user_id && isset($_SESSION['friends']) && !empty($_SESSION['friends'])): ?>
        <div class="mt-5">
            <h2 class="search-title">Mes amis</h2>
            <div class="list-group">
                <?php 
                $friends_list = []; 
                foreach ($_SESSION['friends'] as $friend_id) {
                    $stmt = $bdd->prepare("SELECT id_u, login, email FROM users WHERE id_u = ?");
                    $stmt->execute([$friend_id]);
                    $friend = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($friend) {
                        $friends_list[] = $friend;
                    }
                }
                
                if (empty($friends_list)) {
                    echo "<div class='alert alert-info'>Vous n'avez pas encore d'amis.</div>";
                } else {
                    foreach ($friends_list as $friend) {
                        echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
                        echo "<div>";
                        echo "<h5 class='mb-1'>" . htmlspecialchars($friend['login']) . "</h5>";
                        echo "<small>" . htmlspecialchars($friend['email']) . "</small>";
                        echo "</div>";
                        echo "<a href='profile.php?id=" . $friend['id_u'] . "' class='btn btn-primary btn-sm'><i class='fas fa-user'></i> Voir profil</a>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
