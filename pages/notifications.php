<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

$req_demandes = $bdd->prepare("
    SELECT u.id_u, u.login
    FROM amis a
    JOIN users u ON a.utilisateur_id = u.id_u
    WHERE a.ami_id = :user_id AND a.statut = 'en_attente'
");
$req_demandes->execute(['user_id' => $_SESSION['id_u']]);
$demandes_amis = $req_demandes->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupérer les messages non lus
$req_unread = $bdd->prepare("
    SELECT e.id_exp, u.login, COUNT(*) as nb
    FROM envoyer e
    JOIN users u ON e.id_exp = u.id_u
    WHERE e.id_recept = :user_id AND e.lu = 0
    GROUP BY e.id_exp
");
$req_unread->execute(['user_id' => $user_id]);
$unread_msgs = $req_unread->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les demandes d'amis en attente
$req_demandes = $bdd->prepare("
    SELECT u.id_u, u.login
    FROM amis a
    JOIN users u ON a.utilisateur_id = u.id_u
    WHERE a.ami_id = :user_id AND a.statut = 'en_attente'
");
$req_demandes->execute(['user_id' => $user_id]);
$demandes_amis = $req_demandes->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5 pt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4"> Vos notifications</h3>

            <!-- Notifications de messages -->
            <h5>Messages non lus</h5>
            <?php if (!empty($unread_msgs)): ?>
                <ul class="list-group mb-4">
                    <?php foreach ($unread_msgs as $msg): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="messages.php?contact=<?= $msg['id_exp'] ?>">
                                Nouveau message de <strong><?= htmlspecialchars($msg['login']) ?></strong>
                            </a>
                            <span class="badge bg-primary rounded-pill"><?= $msg['nb'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Aucun nouveau message.</p>
            <?php endif; ?>

            <!-- Notifications de demandes d'amis -->
            <h5>Demandes d’amis</h5>
            <?php if (!empty($demandes_amis)): ?>
                <ul class="list-group">
                    <?php foreach ($demandes_amis as $dem): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><strong><?= htmlspecialchars($dem['login']) ?></strong> vous a envoyé une demande</span>
                            <div>
                                <a href="accepter_ami.php?id=<?= $dem['id_u'] ?>" class="btn btn-sm btn-success">Accepter</a>
                                <a href="refuser.php?id=<?= $dem['id_u'] ?>" class="btn btn-sm btn-danger">Refuser</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Aucune nouvelle demande d’ami.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
<?php ob_end_flush(); ?>

