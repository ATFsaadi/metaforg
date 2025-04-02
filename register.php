<?php
include 'includes/connexion.php';

if(isset($_POST['submit']))
{
    $login = $_POST['login'];
    $email = $_POST['email'];
    $mdp = sha1($_POST['mdp']);

    // Vérification si l'e-mail existe déjà
    $requete = $bdd->prepare("SELECT * FROM users WHERE email = :email");
    $requete->execute(['email' => $email]);
    $reponse = $requete->fetch();
                
    if ($reponse) {
      // Si l'e-mail est déjà utilisé
      echo "<div class='alert alert-danger' role='alert'>Cette adresse e-mail est déjà utilisée.</div>";
    } else {
      // Insertion des données dans la base de données
      
    $bdd->query("INSERT INTO users (login, email, mdp) 
                         VALUES ('$login', '$email', '$mdp')");

    echo "<div class='alert alert-success' role='alert'>Inscription réussie !</div>";
  }
} 
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscription à GamingHub</title>
        <link rel="stylesheet" href="style.css">
        <!-- Ajouter Font Awesome -->
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
            rel="stylesheet">
    </head>
    <body>
        <div class="wrapper">
            <!-- Image à gauche -->
            <div class="image-container">
                <img src="ImgU/logoo.png" alt="Image de présentation">
            </div>

            <!-- Formulaire à droite -->
            <div class="form-container">
                <!-- Ajouter dans le formulaire -->
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="logo">
                    <img src="ImgU/logo.png" alt="GamingHub Logo">
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
        <?php include 'includes/footer.php'?>