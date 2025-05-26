<?php


include '../includes/header-HC.php';
include "../includes/connexion.php";
require_once "../includes/function.php";

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

$message = "";
$valid = false;

if (!empty($token) && !empty($email)) {
    // Vérifie le token et la date d'expiration
    $stmt = $bdd->prepare("SELECT reset_token, reset_expires FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['reset_token'] === $token) {
            if (strtotime($user['reset_expires']) > time()) {
                $valid = true; // token OK et pas expiré
            } else {
                $message = "Lien expiré.";
            }
        } else {
            $message = "Token invalide.";
        }
    } else {
        $message = "Utilisateur non trouvé.";
    }
} else {
    $message = "Lien non valide.";
}

// Si formulaire soumis pour réinitialiser le mdp
if (isset($_POST['submit']) && $valid) {
    $new_mdp = trim($_POST['new_mdp'] ?? '');

    if (strlen($new_mdp) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $hashed_mdp = sha1($new_mdp);
        $update = $bdd->prepare("UPDATE users SET mdp = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
        $update->execute([$hashed_mdp, $email]);

        $message = "Mot de passe réinitialisé avec succès. <a href='../index.php'>Connectez-vous ici</a>.";
        $valid = false; // On masque le formulaire
    }
}
?>

<div class="wrapper">
    <div class="image-container">
        <img src="../assets/images/ImgU/insc.png" alt="Image de présentation">
    </div>
    <div class="form-container">
        <div class="logo">
            <img src="../assets/images/ImgU/logo.png" alt="GamingHub Logo">
        </div>

        <?php if ($message): ?>
            <div class="alert alert-custom mt-0"><?= $message ?></div>
        <?php endif; ?>

        <?php if ($valid): ?>
            <form method="POST" novalidate>
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="form-group">
                    <input type="password" name="new_mdp" placeholder="Nouveau mot de passe" required minlength="6" autofocus>
                </div>
                <button type="submit" name="submit" class="btn">Réinitialiser le mot de passe</button>
            </form>
        <?php else: ?>
            <div class="forgot-password">
                <a href="../index.php">Retour à la connexion</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer-HC.php'; ?>
