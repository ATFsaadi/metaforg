<?php
// Afficher toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Inclure seulement le fichier de connexion à la base de données
include 'includes/connexion.php';

if (isset($_POST['submit'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $mdp = sha1($_POST['mdp']); 

    // Vérifie si l'e-mail existe déjà
    $requete = $bdd->prepare("SELECT * FROM users WHERE email = :email");
    $requete->execute(['email' => $email]);
    $reponse = $requete->fetch();
                
    if ($reponse) {
        $message = "Cette adresse e-mail est déjà utilisée.";
    } else {
        // Insertion avec prepare
        $stmt = $bdd->prepare("INSERT INTO users (login, email, mdp) VALUES (:login, :email, :mdp)");
        $stmt->execute([
            'login' => $login,
            'email' => $email,
            'mdp' => $mdp
        ]);

        // Récupère l'utilisateur nouvellement créé
        $userId = $bdd->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['login'] = $login;
        $_SESSION['email'] = $email;

        $message = "Inscription réussie ! Redirection en cours...";
        header("refresh:3;url=home.php");
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GamingHub - Inscription</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .wrapper {
            display: flex;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            overflow: hidden;
        }
        .image-container {
            flex: 1;
            background-color: var(--bg-secondary);
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .image-container img {
            width: 100%;
            height: auto;
            max-height: 100%;
            display: block;
        }
        .form-container {
            flex: 1;
            background: var(--bg-primary);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .logo img {
            width: 175px;
            height: auto;
            margin-bottom: 20px;
            display: block;
        }
        .form-group {
            margin-bottom: 10px;
            width: 100%;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--text-color);
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            background-color: var(--bg-secondary);
            color: #FFFFFF;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: var(--color-primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: var(--color-primary-hover);
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: var(--text-color);
        }
        .login-link a {
            color: var(--color-success); /* Utilisation de la couleur verte #00FF00 pour les liens */
            text-decoration: none;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        .divider span {
            flex: 1;
            height: 1px;
            background-color: var(--text-color);
        }
        .divider p {
            margin: 0 10px;
            font-size: 12px;
            color: var(--text-color);
        }
        .alert {
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
            background-color: var(--color-success); /* Utilisation de la couleur verte pour les alertes */
            color: var(--bg-primary);
        }
    </style>
</head>
<body>
    <?php if(isset($message)): ?>
    <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="wrapper">
        <!-- Image à gauche -->
        <div class="image-container">
            <img src="assets/images/logoo.png" alt="Image de présentation">
        </div>

        <!-- Formulaire à droite -->
        <div class="form-container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="GamingHub Logo">
            </div>
            <form method="post">
                <div class="form-group">
                    <input
                        name="login"
                        type="text"
                        placeholder="Nom d'utilisateur"
                        required="required">
                </div>
                <div class="form-group">
                    <input
                        name="email"
                        type="email"
                        placeholder="Adresse e-mail"
                        required="required">
                </div>
                <div class="form-group">
                    <input
                        name="mdp"
                        type="password"
                        placeholder="Mot de passe"
                        required="required">
                </div>
                <div class="form-group">
                    <input
                        type="password"
                        placeholder="Confirmer le mot de passe"
                        required="required">
                </div>
                <button name="submit" type="submit" class="btn">S'inscrire</button>
            </form>
            <div class="divider">
                <span></span>
                <p>OU</p>
                <span></span>
            </div>
            <div class="login-link">
                <p>Vous avez déjà un compte ?
                    <a href="index.php">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>