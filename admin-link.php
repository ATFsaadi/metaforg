<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lien d'administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px; 
            background-color: #121212;
            color: white;
        }
        .admin-link {
            display: inline-block;
            padding: 15px 25px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            margin: 20px 0;
        }
        .card {
            background-color: #1e1e1e;
            color: white;
            border: none;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administration Metaforge</h1>
        
        <div class="card p-4">
            <h2><i class="fas fa-user-shield"></i> Accès au panneau d'administration</h2>
            <p>Cliquez sur le bouton ci-dessous pour accéder au panneau d'administration :</p>
            
            <a href="pages/admin.php" class="admin-link">
                <i class="fas fa-cog"></i> Accéder à l'administration
            </a>
            
            <div class="mt-4">
                <p>Vous êtes connecté en tant que : <strong><?= $_SESSION['login'] ?? 'Invité' ?></strong></p>
                <p>Votre niveau d'accès : <strong><?= $_SESSION['lvl'] ?? 'Non défini' ?></strong></p>
            </div>
        </div>
        
        <div class="card p-4">
            <h3>Navigation rapide</h3>
            <div class="row">
                <div class="col-6">
                    <a href="home.php" class="btn btn-outline-light w-100 mb-2">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </div>
                <div class="col-6">
                    <a href="session-test.php" class="btn btn-outline-info w-100 mb-2">
                        <i class="fas fa-info-circle"></i> Test de session
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>