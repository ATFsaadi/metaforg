<?php 
ob_start();
session_start(); // 🔹 Ajoute ça pour utiliser $_SESSION

include 'includes/header-HC.php';
include 'includes/connexion.php';

if (isset($_POST['submit'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $mdp = sha1($_POST['mdp']); // 

    // Vérifie si l'e-mail existe déjà
    $requete = $bdd->prepare("SELECT * FROM users WHERE email = :email");
    $requete->execute(['email' => $email]);
    $reponse = $requete->fetch();
                
    if ($reponse) {
        echo "<div class='alert alert-custom mt-0' role='alert'>Cette adresse e-mail est déjà utilisée.</div>";
    } else {
        // 🔹 Insertion avec prepare (meilleure sécurité)
        $stmt = $bdd->prepare("INSERT INTO users (login, email, mdp) VALUES (:login, :email, :mdp)");
        $stmt->execute([
            'login' => $login,
            'email' => $email,
            'mdp' => $mdp
        ]);

        // 🔹 Récupère l'utilisateur nouvellement créé (avec lastInsertId)
        $userId = $bdd->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['login'] = $login;
        $_SESSION['email'] = $email;

        echo "<div class='alert alert-custom mt-0' role='alert'>Inscription réussie ! Redirection en cours...</div>";
        echo "<meta http-equiv='refresh' content='3;url=home.php'>";
    }
}
?>
<title>Register - MetaForg</title>
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
<?php include 'includes/footer-HC.php'?>