<?php
ob_start();
session_start();
require_once "function.php";
include "includes/connexion.php";

if(isset($_POST['submit']))
{
    $email = $_POST['email'];
    $mdp = sha1($_POST['mdp']);

    $requete = $bdd->query("SELECT * FROM users 
                            WHERE email = '$email' 
                            AND mdp = '$mdp'");

    if($reponse = $requete->fetch())
    {
        $_SESSION['connecte'] = true;
        $_SESSION['id_u'] = $reponse['id_u'];
        $_SESSION['login'] = $reponse['login'];
        $_SESSION['lvl'] = $reponse['lvl'];
        header("Location:home.php");
    }
    else
    {
        echo "<div class='alert alert-danger' role='alert'>
                    Identifiants incorrects
                </div>";
        
    }
}
    
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion à GamingHub</title>
        <link rel="stylesheet" href="assets/css/style.css">
        <!-- Ajouter Font Awesome -->
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
            rel="stylesheet">
    </head>

    <body>
        <div class="wrapper">
            <!-- Image à gauche -->
            <div class="image-container">
                <img src="ImgU/insc.png" alt="Image de présentation">
            </div>

            <!-- Formulaire à droite -->
            <div class="form-container">
                <div class="logo">
                    <img src="ImgU/logo.png" alt="GamingHub Logo">
                </div>
                <form action="#" method="POST">
                    <div class="form-group">
                        <input name="email" type="email" placeholder="Email" required="required">
                    </div>
                    <div class="form-group">
                        <input
                            name="mdp"
                            type="password"
                            placeholder="Mot de passe"
                            required="required">
                    </div>
                    <button name="submit" type="submit" class="btn">Se connecter</button>
                </form>
                <div class="forgot-password">
                    <a href="#">Mot de passe oublié ?</a>
                </div>
                <div class="divider">
                    <span></span>
                    <p>OU</p>
                    <span></span>
                </div>
                <div class="signup-link">
                    <p>Pas encore de compte ?
                        <a href="register.php">Inscrivez-vous</a>
                    </p>
                </div>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>