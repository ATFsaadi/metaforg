<?php
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

if (!isset($_SESSION['login'], $_SESSION['id_u'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupérer les amis acceptés
$query = $bdd->prepare("
    SELECT amis.id as relation_id, users.id_u, users.login 
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

// Récupérer les demandes envoyées en attente
$query_pending = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON users.id_u = amis.ami_id
    WHERE amis.utilisateur_id = :user_id
    AND amis.statut = 'en_attente'
");
$query_pending->execute(['user_id' => $user_id]);
$amis_pending = $query_pending->fetchAll();

// Récupérer les demandes reçues en attente
$query_received = $bdd->prepare("
    SELECT users.id_u, users.login 
    FROM amis
    JOIN users ON users.id_u = amis.utilisateur_id
    WHERE amis.ami_id = :user_id
    AND amis.statut = 'en_attente'
");
$query_received->execute(['user_id' => $user_id]);
$amis_received = $query_received->fetchAll();
?>
<h2 class="section-title text-center">Vos amis</h2>

<div class="container">
    <div class="row justify-content-center gap-4">

        <!-- Bloc Amis -->
        <div class="publication-card col-md-4">
            <div class="post-header d-flex justify-content-between align-items-center">
                Liste des amis
                <span class="badge"><?= count($amis) ?></span>
            </div>
            <ul class="post-content list-unstyled">
                <?php if (!empty($amis)): ?>
                    <?php foreach ($amis as $ami): ?>
                        <li class="notification d-flex justify-content-between align-items-center">
                            <a href="profil.php?id=<?= urlencode($ami['id_u']) ?>" class="notification-link flex-grow-1">
                                <?= htmlspecialchars($ami['login']) ?>
                            </a>
                            <form method="post" action="supprimer-ami.php" class="mb-0">
                                <input type="hidden" name="relation_id" value="<?= $ami['relation_id'] ?>">
                                <input type="hidden" name="ami_id" value="<?= $ami['id_u'] ?>">
                             <button
    type="submit"
    class="btn btn-sm btn-outline-danger"
    style="background: none; border: none; padding: 0; margin: 0; box-shadow: none;"
    onclick="return confirm('Supprimer cet ami ?');"
    title="Supprimer">
    <i class="fas fa-trash" 
       style="color: rgb(255, 60, 0); background: none !important;"></i>
</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-center text-muted">Aucun ami trouvé.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Bloc Demandes envoyées -->
        <div class="publication-card col-md-4">
            <div class="post-header d-flex justify-content-between align-items-center">
                Demandes envoyées
                <span class="badge"><?= count($amis_pending) ?></span>
            </div>
            <ul class="post-content list-unstyled">
                <?php if (!empty($amis_pending)): ?>
                    <?php foreach ($amis_pending as $pending): ?>
                        <li class="notification">
                            <a href="profil.php?id=<?= urlencode($pending['id_u']) ?>" class="notification-link">
                                <?= htmlspecialchars($pending['login']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-center text-muted">Aucune demande en attente.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Bloc Demandes reçues -->
        <div class="publication-card col-md-4">
            <div class="post-header d-flex justify-content-between align-items-center">
                Demandes reçues
                <span class="badge"><?= count($amis_received) ?></span>
            </div>
            <ul class="post-content list-unstyled">
                <?php if (!empty($amis_received)): ?>
                    <?php foreach ($amis_received as $received): ?>
                        <li class="notification d-flex justify-content-between align-items-center">
                            <a href="profil.php?id=<?= urlencode($received['id_u']) ?>" class="notification-link flex-grow-1">
                                <?= htmlspecialchars($received['login']) ?>
                            </a>
                            <div class="d-flex gap-2">
                                <a href="accepter_ami.php?id=<?= $received['id_u'] ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Accepter
                                </a>
                                <a href="refuser.php?id=<?= $received['id_u'] ?>" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i> Refuser
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-center text-muted">Aucune demande reçue.</li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</div>
<?php
include "../includes/mini-messagerie.php";
include '../includes/footer.php';
?>