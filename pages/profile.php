<?php
require_once "../includes/connexion.php";

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$profile_id = intval($_GET['id']);

// Récupérer les informations du profil
$stmt = $bdd->prepare("SELECT id_u, login, email, lvl FROM users WHERE id_u = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'existe pas, rediriger
if (!$user) {
    header('Location: ../index.php?error=Utilisateur introuvable');
    exit;
}

// Vérifier si l'utilisateur est un ami
$is_friend = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['friends']) && in_array($profile_id, $_SESSION['friends'])) {
    $is_friend = true;
}

$title = "Profil de " . htmlspecialchars($user['login']);
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
        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #222;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.2);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            background-color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            color: #00FF00;
        }
        
        .profile-title {
            color: #00FF00;
        }
        
        .profile-info {
            margin: 30px 0;
        }
        
        .info-label {
            color: #00FF00;
            font-weight: bold;
        }
        
        .friend-button {
            background-color: #00FF00;
            border-color: #00FF00;
            color: #000;
        }
        
        .friend-button:hover {
            background-color: #00CC00;
            border-color: #00CC00;
        }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="profile-title"><?php echo htmlspecialchars($user['login']); ?></h1>
            <?php if ($user['lvl'] > 0): ?>
                <span class="badge bg-warning text-dark">Administrateur</span>
            <?php endif; ?>
        </div>
        
        <div class="profile-info">
            <div class="row mb-3">
                <div class="col-4">
                    <span class="info-label">Email :</span>
                </div>
                <div class="col-8">
                    <?php echo htmlspecialchars($user['email']); ?>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $profile_id): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php if (!$is_friend): ?>
                        <a href="add_friend.php?friend_id=<?php echo $user['id_u']; ?>" class="btn btn-success friend-button">
                            <i class="fas fa-user-plus"></i> Ajouter en ami
                        </a>
                    <?php else: ?>
                        <span class="btn btn-outline-success disabled">
                            <i class="fas fa-check"></i> Déjà ami
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="search.php" class="btn btn-primary">
                <i class="fas fa-search"></i> Retour à la recherche
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
