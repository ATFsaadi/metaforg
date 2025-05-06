<?php 
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifie si l'utilisateur est admin (niveau 2 ou plus)
if (!isset($_SESSION['lvl']) || $_SESSION['lvl'] < 2) {
    header("Location: index.php");
    exit; // Toujours arrêter le script après une redirection
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


<h1>Bienvenue sur la page admin</h1>
<p>Tu as les droits nécessaires pour voir cette page.</p>

<?php include "includes/footer.php" ?>
