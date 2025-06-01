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

<h2 class="section-title text-center">Vos notifications</h2>
<div id="notifications-page">
    <!-- Conteneur Bootstrap -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Bloc Messages non lus -->
            <div class="col-md-6 mb-4">
                <?php if (!empty($unread_msgs)): ?>
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-envelope"></i>
                        Messages non lus
                    </h2>
                    <ul class="notifications-list">
                        <?php foreach ($unread_msgs as $msg): ?>
                        <li class="notification d-flex align-items-center">
                            <span class="count-badge"><?= $msg['nb'] ?></span>
                            <a href="messages.php?contact=<?= $msg['id_exp'] ?>" class="notification-link">
                                Nouveau message de
                                <strong><?= htmlspecialchars($msg['login']) ?></strong>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-envelope"></i>
                        Messages non lus
                    </h2>
                    <p class="empty-message">Aucun nouveau message.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bloc Demandes d'amis -->
            <div class="col-md-6 mb-4">
                <?php if (!empty($demandes_amis)): ?>
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-user-friends"></i>
                        Demandes d'amis
                    </h2>
                    <ul class="notifications-list">
                        <?php foreach ($demandes_amis as $dem): ?>
                        <li class="notification">
                            <span class="notification-link">
                                <strong><?= htmlspecialchars($dem['login']) ?></strong>
                                vous a envoyé une demande
                            </span>
                            <div>
                                <a href="accepter_ami.php?id=<?= $dem['id_u'] ?>" class="action-btn accept-btn">Accepter</a>
                                <a href="refuser.php?id=<?= $dem['id_u'] ?>" class="action-btn reject-btn">Refuser</a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-user-friends"></i>
                        Demandes d'amis
                    </h2>
                    <p class="empty-message">Aucune nouvelle demande d'ami.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
<?php ob_end_flush(); ?>