<?php
include "connexion.php";



// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id_u'])) {
    header("Location: login.php");
    exit;
}

// Compter messages non lus
$req_nb_msgs = $bdd->prepare("SELECT COUNT(*) FROM envoyer WHERE id_recept = :uid AND lu = 0");
$req_nb_msgs->execute(['uid' => $_SESSION['id_u']]);
$nb_msgs = $req_nb_msgs->fetchColumn();

// Compter demandes d’amis en attente
$req_nb_demandes = $bdd->prepare("SELECT COUNT(*) FROM amis WHERE ami_id = :uid AND statut = 'en_attente'");
$req_nb_demandes->execute(['uid' => $_SESSION['id_u']]);
$nb_demandes = $req_nb_demandes->fetchColumn();

// Total
$nb_notifications = $nb_msgs + $nb_demandes;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil - MetaForg</title>

        <!-- Bootstrap CSS -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
            rel="stylesheet">

        <!-- Font Awesome pour les icônes -->
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

        <!-- Styles personnalisés -->
        <link rel="stylesheet" href="../assets/css/style-PG.css">
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="../home.php">
                    <strong><img src="../ImgU/logoAcc.png" width="60px" alt="">MetaForg</strong>
                </a>

                <!-- Formulaire de recherche avec une icône de loupe -->
                <div class="d-flex align-items-center ms-auto me-2">
                    <form class="d-flex me-2" action="../pages/recherche.php" method="GET">
                        <div class="input-group">
                            <input
                                type="search"
                                class="form-control rounded-pill"
                                placeholder="Rechercher sur MetaForg..."
                                aria-label="Search"
                                name="q"
                                value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"/>
                            <button class="search-btn input-group-text" type="submit">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="26px"
                                    viewbox="0 0 64 64"
                                    class="search-icon">
                                    <path
                                        d="M 28.300781 10.800781 C 17.100781 10.800781 7.9003906 20.000391 7.9003906 31.400391 C 7.9003906 42.800391 17.000781 52 28.300781 52 C 33.500781 52 38.200781 50.000781 41.800781 46.800781 L 43 48 L 41.900391 49.099609 C 41.300391 49.699609 41.300391 50.700781 41.900391 51.300781 L 50.900391 60.400391 C 51.200391 60.700391 51.6 60.900391 52 60.900391 C 52.4 60.900391 52.8 60.800391 53 60.400391 L 57 56.400391 C 57.5 55.700391 57.500391 54.799219 56.900391 54.199219 L 47.900391 45.099609 C 47.600391 44.799609 47.200781 44.699219 46.800781 44.699219 C 46.400781 44.699219 46.000781 44.799609 45.800781 45.099609 L 44.800781 46.199219 L 43.599609 45 C 46.799609 41.3 48.699219 36.600391 48.699219 31.400391 C 48.699219 20.000391 39.500781 10.800781 28.300781 10.800781 z M 28.300781 13.900391 C 37.900781 13.900391 45.699219 21.8 45.699219 31.5 C 45.699219 41.2 37.900781 49 28.300781 49 C 18.700781 49 10.900391 41.2 10.900391 31.5 C 10.900391 21.8 18.700781 13.900391 28.300781 13.900391 z M 28.400391 20.099609 C 23.600391 20.099609 19.400781 23.099609 17.800781 27.599609 C 17.500781 28.299609 17.9 29.100781 18.5 29.300781 C 18.6 29.300781 18.8 29.400391 19 29.400391 C 19.5 29.400391 20.000781 29 20.300781 28.5 C 21.500781 25 24.800391 22.699219 28.400391 22.699219 C 29.100391 22.699219 29.699219 22.100391 29.699219 21.400391 C 29.699219 20.700391 29.100391 20.099609 28.400391 20.099609 z M 18.900391 32.5 C 18.200391 32.5 17.599609 33.000781 17.599609 33.800781 L 17.599609 34 C 17.599609 34.7 18.100391 35.300781 18.900391 35.300781 C 19.600391 35.300781 20.199219 34.7 20.199219 34 L 20.199219 33.800781 C 20.199219 33.000781 19.700391 32.5 18.900391 32.5 z M 46.900391 48.300781 L 53.800781 55.300781 L 51.900391 57.199219 L 45 50.199219 L 46.900391 48.300781 z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

            </form>
        </div>

        <div class="d-flex align-items-center">
            <a
                href="../pages/profil.php?id=<?= $_SESSION['id_u'] ?>"
                class="text-decoration-none me-3">
                <i class="fas fa-user"></i>
            </a>
            <a href="../pages/amis.php" class="text-decoration-none me-3">
                <i class="fas fa-user-friends"></i>
            </a>
            <a href="../pages/messages.php" class="text-decoration-none me-3">
                <i class="fas fa-envelope" style=""></i>
            </a>
            <a href="../pages/notifications.php" class="text-decoration-none me-3 position-relative">
    <i class="fas fa-bell fa-lg"></i>
    <?php if ($nb_notifications > 0): ?>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $nb_notifications ?>
        </span>
    <?php endif; ?>
</a>

            <?php 
            // Debug pour identifier pourquoi l'élément n'apparait pas
            $admin_visible = false;
            if (isset($_SESSION['lvl'])) {
                $admin_level = intval($_SESSION['lvl']);
                if ($admin_level > 3) {
                    $admin_visible = true;
                }
            }
            ?>
            
            <?php if ($admin_visible): ?>
            <a href="../pages/admin.php" class="text-decoration-none me-3" title="Administration">
                <i class="fas fa-cog fa-lg text-danger"></i>
            </a>
            <?php else: ?>
            <!-- L'icône d'administration n'est pas affichée car le niveau n'est pas > 3 -->
            <a href="../admin-link.php" class="text-decoration-none me-3" title="Administration (accès direct)">
                <i class="fas fa-cog fa-lg"></i>
            </a>
            <?php endif; ?>
            <div class="dropdown">
                <a
                    class="dropdown-toggle text-decoration-none"
                    href="#"
                    role="button"
                    id="userMenuDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <strong class="me-1"><?= strtoupper(htmlspecialchars($_SESSION['login'])) ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDropdown">
                    <li>
                        <a class="dropdown-item" href="../pages/profil.php?id=<?= $_SESSION['id_u'] ?>">Mon profil</a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="../pages/compte.php">Paramètres</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="../logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>