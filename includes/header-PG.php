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
        <link rel="stylesheet" href="../assets/css/style-PG.css">
    </head>
    <body>
        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg">
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
                