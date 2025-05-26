<?php 
include '../includes/header-HC.php';
include "../includes/connexion.php";
require_once "../includes/function.php";

$message = '';

if (isset($_POST['submit'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $message = "<div class='alert alert-custom mt-0'>Adresse e-mail invalide.</div>";
    } else {
        $req = $bdd->prepare("SELECT * FROM users WHERE email = ?");
        $req->execute([$email]);

        if ($user = $req->fetch()) {
            $token = bin2hex(random_bytes(32));
            $expiration = date("Y-m-d H:i:s", strtotime("+1 hour"));
            echo "Expiration définie : $expiration<br>";

            $url = "http://localhost/metaforg/pages/reinitialiser.php?token=$token&email=" . urlencode($email);

            // Stockage du token
            $update = $bdd->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $update->execute([$token, $expiration, $email]);

            // Affichage du lien (test local)
            $message = "<div class='alert alert-custom mt-0'>
                <strong>Test local :</strong> lien de réinitialisation généré.<br>
                <a href='$url'>$url</a>
            </div>";
        } else {
            $message = "<div class='alert alert-custom mt-0'>Aucun compte ne correspond à cet e-mail.</div>";
        }
    }
}
?>

<div class="wrapper">
    <div class="form-container">
        <div class="logo">
            <img src="../assets/images/ImgU/mdp-oublié.png" alt="GamingHub Logo" style="width: 400px; height: auto;">
        </div>

        <?= $message ?>

        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Votre adresse e-mail" required>
            </div>
            <button type="submit" name="submit" class="btn">Générer le lien</button>
        </form>
        <div class="forgot-password">
            <a href="../index.php">Retour à la connexion</a>
        </div>
    </div>
</div>

<?php include '../includes/footer-HC.php'; ?>
