<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test des variables de session</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .debug { background-color: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Test des variables de session</h1>
    
    <div class="debug">
        <h2>Contenu de la session</h2>
        <?php if (isset($_SESSION) && !empty($_SESSION)): ?>
            <ul>
                <?php foreach($_SESSION as $key => $value): ?>
                    <li><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars(print_r($value, true)) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="error">Aucune variable de session n'est définie.</p>
        <?php endif; ?>
    </div>
    
    <div class="debug">
        <h2>Résultat du test pour l'icône admin</h2>
        <?php if (isset($_SESSION['lvl'])): ?>
            <p>Votre niveau est: <strong><?= $_SESSION['lvl'] ?></strong></p>
            <?php if ($_SESSION['lvl'] > 3): ?>
                <p class="success">✅ Vous devriez voir l'icône d'administration.</p>
            <?php else: ?>
                <p class="error">❌ Votre niveau n'est pas suffisant pour voir l'icône d'administration (niveau > 3 requis).</p>
            <?php endif; ?>
        <?php else: ?>
            <p class="error">❌ La variable de session 'lvl' n'est pas définie.</p>
        <?php endif; ?>
    </div>
    
    <div class="debug">
        <h2>Actions possibles</h2>
        <ul>
            <li><a href="index.php">Retour à la page de connexion</a> (pour vous reconnecter)</li>
            <li><a href="home.php">Retour à la page d'accueil</a></li>
        </ul>
    </div>
    
    <div class="debug">
        <h2>Définir votre niveau (temporaire, pour test uniquement)</h2>
        <form method="post" action="">
            <input type="number" name="new_level" placeholder="Nouveau niveau (ex: 5)" min="0" max="100">
            <button type="submit" name="update_level">Mettre à jour</button>
        </form>
        
        <?php
        if (isset($_POST['update_level']) && isset($_POST['new_level'])) {
            $newLevel = intval($_POST['new_level']);
            $_SESSION['lvl'] = $newLevel;
            echo "<p class='success'>Niveau temporairement défini à $newLevel. Retournez à la page d'accueil pour tester.</p>";
        }
        ?>
    </div>
</body>
</html>
