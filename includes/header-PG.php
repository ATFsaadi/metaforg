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

        <!-- Styles personnalisés -->
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    <body>
        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="../home.php">
                    <strong>MetaForg</strong>
                </a>

                <div class="d-flex align-items-center ms-auto me-2">
                    <form class="d-flex me-2" action="../pages/recherche.php" method="POST">
                        <div class="input-group">
                            <input
                                type="search"
                                class="form-control rounded-pill"
                                placeholder="Rechercher sur MetaForg..."
                                aria-label="Search"
                                name="q">
                            <button class="btn btn-outline-primary rounded-pill ms-2" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center">
                    <a
                        href="../pages/profil.php?id=<?= htmlspecialchars($_SESSION['id_u']) ?>"
                        class="text-decoration-none me-3">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="../pages/amis.php" class="text-decoration-none me-3">
                        <i class="fas fa-user-friends"></i>
                    </a>
                    <a href="../pages/messages.php" class="text-decoration-none me-3">
                        <i class="fas fa-envelope" style=""></i>
                    </a>
                    <a href="#" class="text-decoration-none me-3">
                        <i class="fas fa-bell"></i>
                    </a>
                    <div class="dropdown">
                        <a
                            class="dropdown-toggle text-decoration-none"
                            href="#"
                            role="button"
                            id="userMenuDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <strong class="me-1"><?= htmlspecialchars($_SESSION['login']) ?></strong>
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

        <!-- Contenu principal -->
        <div class="container mt-5 pt-4">
            <div class="row">
                <!-- Sidebar gauche -->
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="sidebar">
                        <div class="list-group mb-4">
                            <a
                                href="../pages/profil.php?id=<?= $_SESSION['id_u'] ?>"
                                class="list-group-item list-group-item-action">
                                <i class="fas fa-user me-2"></i>
                                Mon profil
                            </a>
                            <a href="../pages/amis.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-friends me-2"></i>
                                Mes amis
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-gamepad me-2"></i>
                                Jeux
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-users me-2"></i>
                                Groupes
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Événements
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Fil d'actualité -->
                <div class="col-lg-6">
                    <!-- Section Stories -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-2">
                            <div class="d-flex overflow-auto" style="gap: 15px; padding-bottom: 10px;">
                                <!-- Créer une story -->
                                <div class="text-center" style="min-width: 90px;">
                                    <div class="position-relative">
                                        <div
                                            class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                            style="width: 90px; height: 90px; overflow: hidden;">
                                            <img
                                                src="assets/images/default_avatar.png"
                                                alt="Votre avatar"
                                                class="w-100 h-100"
                                                style="object-fit: cover;">
                                            <div
                                                class="position-absolute bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px; bottom: 5px; right: 5px; border: 3px solid #3b5998;">
                                                <i class="fas fa-plus text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1 small">Créer une story</div>
                                </div>

                                <!-- Stories des amis (exemples) -->
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                <div class="text-center" style="min-width: 90px;">
                                    <div class="position-relative">
                                        <div
                                            class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 90px; height: 90px; overflow: hidden; border: 3px solid #3b5998;">
                                            <img
                                                src="assets/images/default_avatar.png"
                                                alt="Avatar ami"
                                                class="w-100 h-100"
                                                style="object-fit: cover;">
                                        </div>
                                    </div>
                                    <div class="mt-1 small">Ami
                                        <?php echo $i; ?></div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>