<?php 
ob_start();
session_start(); // Démarre la session
include "includes/connexion.php"; 

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header("Location: login.php"); // Redirige vers la page de connexion
    exit;
}
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - MetaForg</title>
    <link rel="stylesheet" href="header.css">
    <!-- Ajoutez FontAwesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <!-- Logo / Accueil -->
        <div class="logo">
            <a href="index.php">MetaForg</a>
        </div>

        <!-- Barre de recherche -->
        <div class="search-bar">
            <input type="text" placeholder="Rechercher sur MetaForg">
        </div>

        <!-- Liens principaux avec icônes -->
        <div class="nav-links">
            <a href="#" class="icon-link" title="Amis">
                <i class="fas fa-user-friends"></i>
            </a>
            <a href="#" class="icon-link" title="Messagerie">
                <i class="fas fa-comment-dots"></i>
            </a>
            <a href="#" class="icon-link" title="Notifications">
                <i class="fas fa-bell"></i>
            </a>
        </div>

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
</body>
</html>