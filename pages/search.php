<?php
require_once "../includes/connexion.php";
$title = "Recherche d'utilisateurs";

// Traitement de la recherche
$resultats = [];
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $query = $_GET['q'];
    $resultats = rechercherUtilisateurs($bdd, $query);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Metaforge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <style>
        .search-title {
            color: #00FF00;
            margin-bottom: 20px;
        }
        .search-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-form {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container search-container">
        <h1 class="search-title">Recherche d'utilisateurs</h1>
        
        <div class="search-form">
            <?php afficherFormulaireRecherche('', 'GET'); ?>
        </div>

        <?php if (isset($_GET['q']) && !empty($_GET['q'])): ?>
            <div class="search-results">
                <h2 class="search-title">Résultats pour "<?php echo htmlspecialchars($_GET['q']); ?>"</h2>
                <?php afficherResultatsRecherche($resultats); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
