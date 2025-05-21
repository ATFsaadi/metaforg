<?php
session_start(); 

// Démarre la session
include "../includes/connexion.php";
include "../includes/header-PG.php"; 

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header("Location: login.php"); // Redirige vers la page de connexion
    exit;
}
?>



        <!-- Menu utilisateur -->
        <div class="user-menu">
            <?php
                $compteAvatarPath = "../assets/images/Profil/profil_{$_SESSION['id_u']}.png";
                if (!file_exists($compteAvatarPath)) {
                    $compteAvatarPath = "../assets/images/default_avatar.png";
                }
            ?>
            <img src="<?= $compteAvatarPath ?>" alt="Avatar" class="avatar">
            <span class="username"><?= htmlspecialchars($_SESSION['login']) ?></span>
            <button class="menu-icon">⛮</button>
        </div>
    </nav>
    <header>
        <h1>Bienvenue sur votre compte, <?php echo htmlspecialchars($_SESSION['login']); ?> !</h1>
    </header>
    <?php include '../includes/footer.php'; ?>
