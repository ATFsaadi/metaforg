<?php

session_start();

include "../includes/connexion.php";
include "../includes/header-PG.php";

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupérer les amis
$query = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis 
    JOIN users ON users.id_u = amis.ami_id
    WHERE amis.utilisateur_id = :user_id
");
$query->execute(['user_id' => $user_id]);
$amis = $query->fetchAll();

?>


<h2>Vos amis</h2>
<ul>
    <?php foreach ($amis as $ami): ?>
        <li><?php echo htmlspecialchars($ami['login']); ?></li>
    <?php endforeach; ?>
</ul>


</body>
<?php include '../includes/footer.php'; ?>
</html>
