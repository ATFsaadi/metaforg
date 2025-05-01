<?php
session_start();

include "includes/connexion.php";
include "../includes/header-PG.php";

$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$req = $bdd->prepare("
    SELECT p.*, u.login 
    FROM publications p
    JOIN users u ON p.user_id = u.id_u
    ORDER BY p.date DESC
    LIMIT :limit OFFSET :offset
");
$req->bindValue(':limit', $limit, PDO::PARAM_INT);
$req->bindValue(':offset', $offset, PDO::PARAM_INT);
$req->execute();
?>

<h2>Fil d'actualité</h2>

<?php foreach ($posts as $post): ?>
    <div>
        <p><strong><?php echo htmlspecialchars($post['login']); ?></strong></p>
        <p><?php echo htmlspecialchars($post['message']); ?></p>
        <?php if ($post['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" width="200">
        <?php endif; ?>
    </div>
<?php endforeach; ?>

</body>
</html>
