<?php
session_start();
include "includes/connexion.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupérer les amis
$query = $pdo->prepare("
    SELECT utilisateurs.id_u, utilisateurs.login 
    FROM amis 
    JOIN utilisateurs ON utilisateurs.id_u = amis.ami_id
    WHERE amis.user_id = :user_id
");
$query->execute(['user_id' => $user_id]);
$amis = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste d'amis</title>
</head>
<body>

<h2>Vos amis</h2>
<ul>
    <?php foreach ($amis as $ami): ?>
        <li><?php echo htmlspecialchars($ami['login']); ?></li>
    <?php endforeach; ?>
</ul>

</body>
</html>
