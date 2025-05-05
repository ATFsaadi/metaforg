<?php
require_once "../includes/connexion.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_u'])) {
    header('Location: ../index.php');
    exit;
}

// Déterminer l'ID du profil à afficher
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_u'];

// Récupérer les informations du profil
$requete = $bdd->prepare("SELECT * FROM users WHERE id_u = ?");
$requete->execute([$profile_id]);
$user = $requete->fetch();

if (!$user) {
    header('Location: ../index.php?error=Utilisateur introuvable');
    exit;
}

// Si c'est le profil de l'utilisateur connecté et qu'il soumet le formulaire
$error = '';
$success = '';

if ($profile_id == $_SESSION['id_u'] && isset($_POST['submit'])) {
    $login = htmlspecialchars($_POST['login']);
    $email = htmlspecialchars($_POST['email']);
    
    // Validation des données
    if (empty($login) || empty($email)) {
        $error = "Tous les champs obligatoires doivent être remplis";
    } else {
        // Si mot de passe fourni, le mettre à jour
        if (!empty($_POST['mdp'])) {
            if ($_POST['mdp'] !== $_POST['mdpConfirm']) {
                $error = "Les mots de passe ne correspondent pas";
            } else {
                // Utiliser SHA1 comme dans votre système existant
                $mdp = sha1($_POST['mdp']);
                $update = $bdd->prepare("UPDATE users SET login = ?, email = ?, mdp = ? WHERE id_u = ?");
                $update->execute([$login, $email, $mdp, $_SESSION['id_u']]);
                $success = "Profil mis à jour avec succès";
                
                // Mettre à jour les données de session
                $_SESSION['login'] = $login;
            }
        } else {
            // Mise à jour sans changer le mot de passe
            $update = $bdd->prepare("UPDATE users SET login = ?, email = ? WHERE id_u = ?");
            $update->execute([$login, $email, $_SESSION['id_u']]);
            $success = "Profil mis à jour avec succès";
            
            // Mettre à jour les données de session
            $_SESSION['login'] = $login;
        }
    }
    
    // Rafraîchir les données
    if (empty($error)) {
        $requete->execute([$profile_id]);
        $user = $requete->fetch();
    }
}

// Vérifier si c'est un ami
$is_friend = false;
if (isset($_SESSION['friends']) && in_array($profile_id, $_SESSION['friends'])) {
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            color: #00FF00;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: #00FF00;
            border-color: #00FF00;
            color: #000;
            font-weight: bold;
        }
        
        .btn-primary:hover {
            background-color: #00CC00;
            border-color: #00CC00;
            color: #000;
        }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container profile-container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="profile-title"><?php echo htmlspecialchars($user['login']); ?></h1>
            <?php if ($user['lvl'] > 0): ?>
                <span class="badge bg-warning text-dark">Administrateur</span>
            <?php endif; ?>
        </div>

        <?php if ($profile_id == $_SESSION['id_u']): ?>
            <!-- Formulaire d'édition si c'est le profil de l'utilisateur connecté -->
            <form method="post" class="mt-4">
                <div class="form-group">
                    <label for="loginInput">Nom d'utilisateur :</label>
                    <input
                        type="text"
                        name="login"
                        value="<?php echo htmlspecialchars($user['login']); ?>"
                        class="form-control"
                        id="loginInput"
                        placeholder="Nom d'utilisateur">
                </div>
                <div class="form-group">
                    <label for="emailInput">Email :</label>
                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>"
                        class="form-control"
                        id="emailInput"
                        placeholder="Adresse email">
                </div>
                <div class="form-group">
                    <label for="passwordInput">Nouveau mot de passe :</label>
                    <input
                        type="password"
                        name="mdp"
                        class="form-control"
                        id="passwordInput"
                        placeholder="Laissez vide pour conserver l'actuel">
                </div>
                <div class="form-group">
                    <label for="passwordConfirmInput">Confirmer le mot de passe :</label>
                    <input
                        type="password"
                        name="mdpConfirm"
                        class="form-control"
                        id="passwordConfirmInput"
                        placeholder="Confirmez le nouveau mot de passe">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">Mettre à jour le profil</button>
                </div>
            </form>
        <?php else: ?>
            <!-- Affichage simple pour le profil d'un autre utilisateur -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title" style="color: #00FF00;">Informations</h5>
                    <p class="card-text"><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <?php if (isset($_SESSION['id_u']) && $_SESSION['id_u'] != $profile_id): ?>
                        <div class="d-grid gap-2 mt-3">
                            <?php if (!$is_friend): ?>
                                <a href="add_friend.php?friend_id=<?php echo $user['id_u']; ?>" class="btn btn-success">Ajouter en ami</a>
                            <?php else: ?>
                                <button class="btn btn-outline-success disabled">Déjà ami</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="../home.php" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>