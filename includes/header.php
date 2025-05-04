<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metaforge</title>
    <!-- Fichiers CSS communs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/assets/css/header.css">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link, .navbar-brand {
            color: #00FF00 !important;
        }
        .search-btn {
            color: #00FF00;
            border-color: #00FF00;
        }
        .search-btn:hover {
            background-color: #00FF00;
            color: #000;
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/index.php">Metaforge</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/index.php">Accueil</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/pages/profile.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/pages/messages.php">Messages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/pages/amis.php">Amis</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/register.php">S'inscrire</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/index.php">Connexion</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <!-- Formulaire de recherche -->
                <form class="d-flex" action="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/pages/search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Rechercher un utilisateur..." aria-label="Search">
                    <button class="btn btn-outline-success search-btn" type="submit">Rechercher</button>
                </form>
                <!-- Afficher bouton de déconnexion si l'utilisateur est connecté -->
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="ms-3">
                    <a href="<?php echo str_replace('/pages', '', str_replace('/includes', '', dirname($_SERVER['PHP_SELF']))); ?>/logout.php" class="btn btn-danger">Déconnexion</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>