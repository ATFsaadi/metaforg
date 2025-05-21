<?php
session_start();
include "connexion.php";

if (!isset($_SESSION['id_u'])) {
    echo "<div class='p-2 text-muted'>Non connecté</div>";
    exit;
}

$user_id = $_SESSION['id_u'];

$req = $bdd->prepare("
    SELECT 
        u.id_u, u.login, u.photo_profil,
        e.message, MAX(e.date_env) as last_date,
        SUM(CASE WHEN e.lu = 0 AND e.id_recept = :user_id THEN 1 ELSE 0 END) as non_lus
    FROM envoyer e
    JOIN users u ON u.id_u = CASE 
        WHEN e.id_exp = :user_id THEN e.id_recept 
        ELSE e.id_exp 
    END
    WHERE e.id_exp = :user_id OR e.id_recept = :user_id
    GROUP BY u.id_u, u.login, u.photo_profil
    ORDER BY last_date DESC
    LIMIT 5
");
$req->execute(['user_id' => $user_id]);
$conversations = $req->fetchAll(PDO::FETCH_ASSOC);

foreach ($conversations as $conv):
?>
    <div class="d-flex align-items-center p-2 border-bottom" onclick="window.location.href='/metaforg/pages/messages.php?contact=<?= $conv['id_u'] ?>'" style="cursor: pointer;">
        <img src="<?= $conv['photo_profil'] ?? 'assets/images/default_avatar.png' ?>" width="40" class="rounded-circle me-2">
        <div class="flex-grow-1">
            <div class="fw-bold"><?= htmlspecialchars($conv['login']) ?></div>
            <div class="small text-muted text-truncate"><?= htmlspecialchars($conv['message']) ?></div>
        </div>
        <?php if ($conv['non_lus'] > 0): ?>
            <span class="badge bg-danger rounded-pill"><?= $conv['non_lus'] ?></span>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
