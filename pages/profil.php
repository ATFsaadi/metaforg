<?php  
ob_start();
session_start();
include "../includes/connexion.php";

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID invalide");
}

$profile_id = (int)$_GET['id'];
$current_user_id = $_SESSION['id_u'] ?? null;

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_user_id === $profile_id) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Jeton CSRF invalide.";
        header("Location: profil.php?id=".$profile_id);
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $langue = $_POST['langue'] ?? '';
    $pays = trim($_POST['pays'] ?? '');
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirmation_mdp = $_POST['confirmation_mdp'] ?? '';

    try {
        $stmt = $bdd->prepare("SELECT * FROM users WHERE id_u = ?");
        $stmt->execute([$current_user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = "Utilisateur introuvable.";
        } else {
            $update = $bdd->prepare("UPDATE users SET email = ?, langue = ?, pays = ? WHERE id_u = ?");
            $update->execute([$email, $langue, $pays, $current_user_id]);
            $_SESSION['success'] = "Profil mis à jour avec succès.";

            if (!empty($ancien_mdp) || !empty($nouveau_mdp) || !empty($confirmation_mdp)) {
                if (!password_verify($ancien_mdp, $user['password'])) {
                    $_SESSION['error'] = "Ancien mot de passe incorrect.";
                } elseif (strlen($nouveau_mdp) < 8) {
                    $_SESSION['error'] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
                } elseif ($nouveau_mdp !== $confirmation_mdp) {
                    $_SESSION['error'] = "La confirmation du mot de passe ne correspond pas.";
                } else {
                    $newHashedPwd = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                    $updatePwd = $bdd->prepare("UPDATE users SET password = ? WHERE id_u = ?");
                    $updatePwd->execute([$newHashedPwd, $current_user_id]);
                    $_SESSION['success'] .= " Mot de passe mis à jour.";
                }
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la mise à jour : " . $e->getMessage();
    }

    header("Location: profil.php?id=" . $profile_id);
    exit;
}

// Traitement de l'ajout d'ami
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_ami'], $_POST['ami_id'])) {
    $utilisateur_id = $_SESSION['id_u'];
    $ami_id = (int)$_POST['ami_id'];

    try {
        if ($utilisateur_id === $ami_id) {
            $_SESSION['friend_message'] = ['type' => 'danger', 'text' => 'Vous ne pouvez pas vous ajouter vous-même'];
        } else {
            $check = $bdd->prepare("SELECT * FROM amis 
                WHERE ((utilisateur_id = ? AND ami_id = ?) OR (utilisateur_id = ? AND ami_id = ?)) 
                AND statut IN ('accepte', 'en_attente')");
            $check->execute([$utilisateur_id, $ami_id, $ami_id, $utilisateur_id]);

            if ($check->rowCount() > 0) {
                $_SESSION['friend_message'] = ['type' => 'warning', 'text' => 'Une demande existe déjà'];
            } else {
                $stmt = $bdd->prepare("INSERT INTO amis (utilisateur_id, ami_id, statut) VALUES (?, ?, 'en_attente')");
                $stmt->execute([$utilisateur_id, $ami_id]);
                $_SESSION['friend_message'] = ['type' => 'success', 'text' => 'Demande envoyée avec succès'];
            }
        }
    } catch (PDOException $e) {
        $_SESSION['friend_message'] = ['type' => 'danger', 'text' => 'Erreur: ' . $e->getMessage()];
    }
    header("Location: profil.php?id=" . $_GET['id']);
    exit;
}

// Récupération des messages
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
$friend_message = $_SESSION['friend_message'] ?? null;
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['friend_message']);

// Récupération des infos utilisateur
$stmt = $bdd->prepare("SELECT * FROM users WHERE id_u = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) die("Utilisateur introuvable");

// Chargement image de profil
$basePath = "../assets/images/Profil/";
$filename = "profil_" . $user['id_u'];
$extensions = ['jpg', 'jpeg', 'png', 'webp'];
$profileImage = $basePath . "default.jpg";

foreach ($extensions as $ext) {
    if (file_exists($basePath . $filename . "." . $ext)) {
        $profileImage = $basePath . $filename . "." . $ext;
        break;
    }
}

include "../includes/header-PG.php";
?>

<div class="profile-container">
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if ($friend_message): ?>
        <div class="alert alert-<?= $friend_message['type'] ?>"><?= $friend_message['text'] ?></div>
    <?php endif; ?>

    <div class="profile-header">
        <img src="<?= $profileImage ?>" alt="Avatar de <?= htmlspecialchars($user['login']) ?>" class="friend-avatar" style="width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 3px solid #ccc;" loading="lazy">

        <?php if ($current_user_id && $current_user_id !== $profile_id): ?>
            <div class="friend-actions mt-3">
                <?php
                $stmt = $bdd->prepare("SELECT id, statut, utilisateur_id FROM amis WHERE (utilisateur_id = ? AND ami_id = ?) OR (utilisateur_id = ? AND ami_id = ?) ORDER BY id DESC LIMIT 1");
                $stmt->execute([$current_user_id, $profile_id, $profile_id, $current_user_id]);
                $relation = $stmt->fetch();
                $status = $relation['statut'] ?? null;
                ?>
                <?php if ($status): ?>
                    <div class="alert <?= $status === 'accepte' ? 'alert-success' : 'alert-success' ?> mt-2" role="alert" style="max-width: 300px;">
                        <?php if ($status === 'accepte'): ?>
                            <i class="fas fa-user-check"></i> Vous êtes déjà amis
                        <?php elseif ($status === 'en_attente'): ?>
                            <i class="fas fa-hourglass-half"></i> Demande d'ami en attente
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($status === 'accepte'): ?>
                    <form method="POST" action="supprimer_ami.php" class="d-inline-block">
                        <input type="hidden" name="relation_id" value="<?= $relation['id'] ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cet ami ?')">
                            <i class="fas fa-user-times"></i> Supprimer
                        </button>
                    </form>
                    <a href="messages.php?contact=<?= $profile_id ?>" class="btn btn-info ml-2">
                        <i class="fas fa-envelope"></i> Contacter
                    </a>
                <?php elseif ($status === 'en_attente'): ?>
                    <!-- Aucune action proposée si demande en attente -->
                <?php else: ?>
                    <form method="POST" class="d-inline-block">
                        <input type="hidden" name="ajouter_ami" value="1">
                        <input type="hidden" name="ami_id" value="<?= $profile_id ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Ajouter ami</button>
                    </form>
                    <a href="messages.php?contact=<?= $profile_id ?>" class="btn btn-info ml-2">
                        <i class="fas fa-envelope"></i> Contacter
                    </a>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>

    <div class="profile-info mt-4">
        <div class="info-item"><span class="label">Email:</span> <span class="value"><?= htmlspecialchars($user['email']) ?></span></div>
        <div class="info-item"><span class="label">Langue:</span> <span class="value">
            <?= match($user['langue']) {
                'fr' => 'Français',
                'en' => 'Anglais',
                'es' => 'Espagnol',
                default => 'Non renseigné'
            } ?>
        </span></div>
        <div class="info-item"><span class="label">Pays:</span> <span class="value"><?= htmlspecialchars($user['pays'] ?? 'Non renseigné') ?></span></div>
    </div>

    <?php if ($current_user_id === $profile_id): ?>
        <div class="update-form mt-5">
            <h2 class="section-title text-center"><i class="fas fa-edit" style="color: #00BFFF;"></i> Modifier profil</h2>
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
                    <h2 class="section-title text-center"><i class="fas fa-lock" style="color: #00BFFF;"></i> Changer mot de passe</h2>
                    <div class="form-group"><label>Ancien mot de passe</label><input type="password" name="ancien_mdp" class="form-control"></div>
                    <div class="form-group"><label>Nouveau mot de passe</label><input type="password" name="nouveau_mdp" class="form-control"><small class="text-muted">Minimum 8 caractères</small></div>
                    <div class="form-group"><label>Confirmation</label><input type="password" name="confirmation_mdp" class="form-control"></div>
                </div>

                <!-- Bouton centré -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include "../includes/mini-messagerie.php"; ?>
<?php include "../includes/footer.php"; ?>
