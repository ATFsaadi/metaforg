<?php
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifie la connexion
if (!isset($_SESSION['login'], $_SESSION['id_u'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupérer les amis acceptés
$query = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON (
        (users.id_u = amis.utilisateur_id AND amis.ami_id = :user_id)
        OR
        (users.id_u = amis.ami_id AND amis.utilisateur_id = :user_id)
    )
    WHERE amis.statut = 'accepte'
");
$query->execute(['user_id' => $user_id]);
$amis = $query->fetchAll();

// Récupérer les demandes en attente
$query_pending = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON users.id_u = amis.ami_id
    WHERE amis.utilisateur_id = :user_id
    AND amis.statut = 'en_attente'
");
$query_pending->execute(['user_id' => $user_id]);
$amis_pending = $query_pending->fetchAll();
?>

<h2>Vos amis</h2>
<div class="d-flex">
    <div class="col-8">
        <ul>
            <?php foreach ($amis as $ami): ?>
                <li>
                    <a href="profil.php?id=<?= urlencode($ami['id_u']) ?>">
                        <?= htmlspecialchars($ami['login']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="col-4">
        <h3>Demandes envoyées</h3>
        <ul>
            <?php if (empty($amis_pending)): ?>
                <li>Aucune demande en attente.</li>
            <?php else: ?>
                <?php foreach ($amis_pending as $pending): ?>
                    <li>
                        <a href="profil.php?id=<?= urlencode($pending['id_u']) ?>">
                            <?= htmlspecialchars($pending['login']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
