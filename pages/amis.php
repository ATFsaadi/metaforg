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

// Récupérer les amis acceptés (avec l'id de la relation pour suppression)
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
      <div class="post-header">Liste des amis</div>
      <ul class="post-content">
        <?php foreach ($amis as $ami): ?>
          <li>
            <a href="profil.php?id=<?= urlencode($ami['id_u']) ?>">
              <?= htmlspecialchars($ami['login']) ?>
            </a>
            <!-- Formulaire suppression ami -->
            <form method="post" action="supprimer-ami.php" style="display:inline;">
              <input type="hidden" name="relation_id" value="<?= $ami['relation_id'] ?>">
              <input type="hidden" name="ami_id" value="<?= $ami['id_u'] ?>">
              <button type="submit" style="color:red; border:none; background:none; cursor:pointer;" onclick="return confirm('Supprimer cet ami ?');">✖️</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Bloc Demandes envoyées -->
    <div class="publication-card col-md-4">
      <div class="post-header">Demandes envoyées</div>
      <ul class="post-content">
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

    <!-- Bloc Demandes reçues -->
    <div class="publication-card col-md-4">
      <div class="post-header">Demandes reçues</div>
      <ul class="post-content">
        <?php if (empty($amis_received)): ?>
          <li>Aucune demande reçue.</li>
        <?php else: ?>
          <?php foreach ($amis_received as $received): ?>
            <li>
              <a href="profil.php?id=<?= urlencode($received['id_u']) ?>">
                <?= htmlspecialchars($received['login']) ?>
              </a>
              <!-- Boutons accepter / refuser -->
              <a href="accepter_ami.php?id=<?= $received['id_u'] ?>" style="color:green; margin-left:10px;">Accepter</a>
              <a href="refuser.php?id=<?= $received['id_u'] ?>" style="color:red; margin-left:5px;">Refuser</a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<?php
include "../includes/mini-messagerie.php";
include '../includes/footer.php';
?>
