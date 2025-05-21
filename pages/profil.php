<?php 
ob_start();
session_start();
include "../includes/connexion.php";

// Protection CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Traitement de l'ajout d'ami
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_ami'], $_POST['ami_id'])) {
    $utilisateur_id = $_SESSION['id_u'];
    $ami_id = (int)$_POST['ami_id'];

    try {
        if ($utilisateur_id === $ami_id) {
            $_SESSION['friend_message'] = [
                'type' => 'danger',
                'text' => 'Vous ne pouvez pas vous ajouter vous-même'
            ];
        } else {
            $check = $bdd->prepare("SELECT * FROM amis WHERE (utilisateur_id=? AND ami_id=?) OR (utilisateur_id=? AND ami_id=?)");
            $check->execute([$utilisateur_id, $ami_id, $ami_id, $utilisateur_id]);

            if ($check->rowCount() > 0) {
                $_SESSION['friend_message'] = [
                    'type' => 'warning',
                    'text' => 'Une demande existe déjà'
                ];
            } else {
                $stmt = $bdd->prepare("INSERT INTO amis (utilisateur_id, ami_id, statut) VALUES (?, ?, 'en_attente')");
                $stmt->execute([$utilisateur_id, $ami_id]);
                $_SESSION['friend_message'] = [
                    'type' => 'success',
                    'text' => 'Demande envoyée avec succès'
                ];
            }
        }
    } catch (PDOException $e) {
        $_SESSION['friend_message'] = [
            'type' => 'danger',
            'text' => 'Erreur: ' . $e->getMessage()
        ];
    }
    header("Location: profil.php?id=".$_GET['id']);
    exit;
}

// Récupération des messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
$friend_message = $_SESSION['friend_message'] ?? null;
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['friend_message']);

// Vérification de l'ID du profil
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID invalide");
}

$profile_id = (int)$_GET['id'];
$current_user_id = $_SESSION['id_u'] ?? null;

// Récupération des données utilisateur
$stmt = $bdd->prepare("SELECT * FROM users WHERE id_u = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Utilisateur introuvable");
}

// Détection de la photo de profil
$basePath = "../assets/images/Profil/";
$filename = "profil_" . $user['id_u'];
$extensions = ['jpg', 'jpeg', 'png', 'webp'];
$profileImage = $basePath . "default.jpg"; // Image par défaut

foreach ($extensions as $ext) {
    if (file_exists($basePath . $filename . "." . $ext)) {
        $profileImage = $basePath . $filename . "." . $ext;
        break;
    }
}

include "../includes/header-PG.php";
?>

<div class="profile-container">
    <!-- Affichage des messages -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($friend_message): ?>
        <div class="alert alert-<?= $friend_message['type'] ?>">
            <?= $friend_message['text'] ?>
        </div>
    <?php endif; ?>

    <div class="profile-header">
        <img src="<?= $profileImage ?>" 
             alt="Avatar de <?= htmlspecialchars($user['login']) ?>" 
              style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 3px solid #ccc;"
             class="friend-avatar"
             loading="lazy">

        <?php if ($current_user_id && $current_user_id != $profile_id): ?>
            <div class="friend-actions mt-3">
                <?php
                $relationStmt = $bdd->prepare("
                    SELECT id, statut, utilisateur_id FROM amis 
                    WHERE (utilisateur_id=? AND ami_id=?) OR (utilisateur_id=? AND ami_id=?)
                    ORDER BY id DESC LIMIT 1
                ");
                $relationStmt->execute([$current_user_id, $profile_id, $profile_id, $current_user_id]);
                $relation = $relationStmt->fetch();
                $status = $relation['statut'] ?? null;
                ?>
                
                <?php if ($status === 'accepte'): ?>
                    <form method="POST" action="supprimer_ami.php" class="d-inline-block">
                        <input type="hidden" name="relation_id" value="<?= $relation['id'] ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cet ami?')">
                            <i class="fas fa-user-times"></i> Supprimer
                        </button>
                    </form>
                <?php elseif ($status === 'en_attente'): ?>
                    <?php if ($relation['utilisateur_id'] == $current_user_id): ?>
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-clock"></i> Demande envoyée
                        </button>
                    <?php else: ?>
                        <a href="accepter.php?id=<?= $profile_id ?>" class="btn btn-success">
                            <i class="fas fa-check"></i> Accepter
                        </a>
                        <a href="refuser.php?id=<?= $profile_id ?>" class="btn btn-danger ml-2">
                            <i class="fas fa-times"></i> Refuser
                        </a>
                    <?php endif; ?>
                <?php elseif (!$status): ?>
                    <form method="POST" class="d-inline-block">
                        <input type="hidden" name="ajouter_ami" value="1">
                        <input type="hidden" name="ami_id" value="<?= $profile_id ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Ajouter ami
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="profile-info mt-4">
        <div class="info-item">
            <span class="label">Email:</span>
            <span class="value"><?= htmlspecialchars($user['email']) ?></span>
        </div>
      
        <div class="info-item">
            <span class="label">Langue:</span>
            <span class="value">
                <?= match($user['langue']) {
                    'fr' => 'Français',
                    'en' => 'Anglais', 
                    'es' => 'Espagnol',
                    default => 'Non renseigné'
                } ?>
            </span>
        </div>
        <div class="info-item">
            <span class="label">Pays:</span>
            <span class="value"><?= htmlspecialchars($user['pays'] ?? 'Non renseigné') ?></span>
        </div>
    </div>

    <?php if ($current_user_id === $profile_id): ?>
        <div class="update-form mt-5">
            <h3><i class="fas fa-edit"></i> Modifier profil</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Langue</label>
                    <select name="langue" class="form-control">
                        <option value="fr" <?= $user['langue'] === 'fr' ? 'selected' : '' ?>>Français</option>
                        <option value="en" <?= $user['langue'] === 'en' ? 'selected' : '' ?>>Anglais</option>
                        <option value="es" <?= $user['langue'] === 'es' ? 'selected' : '' ?>>Espagnol</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Pays</label>
                    <input type="text" name="pays" value="<?= htmlspecialchars($user['pays'] ?? '') ?>" class="form-control">
                </div>
                
                <div class="password-section mt-4 border-top pt-3">
                    <h3><i class="fas fa-lock"></i> Changer mot de passe</h3>
                    
                    <div class="form-group">
                        <label>Ancien mot de passe</label>
                        <input type="password" name="ancien_mdp" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="nouveau_mdp" class="form-control">
                        <small class="text-muted">Minimum 8 caractères</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirmation</label>
                        <input type="password" name="confirmation_mdp" class="form-control">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
