<?php
ob_start();
session_start();
include 'includes/header-HC.php';
include "includes/connexion.php";
require_once "includes/function.php";


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
        echo "<div class='alert alert-custom mt-0' role='alert'>
                    Identifiants incorrects
                </div>";
        
    }
}
    
?>

<div class="wrapper">
    <div class="image-container">
        <img src="assets/images/ImgU/insc.png" alt="Image de présentation">
    </div>
    <div class="form-container">
        <div class="logo">
            <img src="assets/images/ImgU/logo.png" alt="GamingHub Logo">
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
            <a href="pages/mdp-oublier.php">Mot de passe oublié ?</a>
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

<?php include 'includes/footer-HC.php'; ?>
