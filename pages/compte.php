<?php
session_start(); 

include "../includes/connexion.php";
include "../includes/header-PG.php"; 

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>

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
