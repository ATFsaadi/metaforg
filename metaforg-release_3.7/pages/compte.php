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
            <img src="avatar.jpg" alt="Avatar" class="avatar">
            <span class="username">Nom Prénom</span>
            <button class="menu-icon">⋮</button>
        </div>
    </nav>
    <header>
        <h1>Bienvenue sur votre compte, <?php echo htmlspecialchars($_SESSION['login']); ?> !</h1>
    </header>
    <?php include '../includes/footer.php'; ?>
