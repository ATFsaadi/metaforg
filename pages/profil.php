<?php
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['id_u']) && isset($_POST['email'])) {
        $user_id = $_SESSION['id_u'];
        
        // Nettoyage des données
        $email = htmlspecialchars($_POST['email']);
        $age = intval($_POST['age']);
        $langue = htmlspecialchars($_POST['langue']);
        $pays = htmlspecialchars($_POST['pays']);

        try {
            $stmt = $bdd->prepare("UPDATE users SET 
                email = ?, 
                age = ?, 
                langue = ?, 
                pays = ? 
                WHERE id_u = ?");
            
            $stmt->execute([$email, $age, $langue, $pays, $user_id]);
            
            $_SESSION['success'] = "Profil mis à jour avec succès !";
            // header("Location: profile.php?id=" . $user_id);
            // exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur de mise à jour : " . $e->getMessage();
            header("Location: profile.php?id=" . $user_id);
            exit();
        }
    }
}

// Messages de feedback
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// Vérification ID profil
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $profile_id = (int)$_GET['id'];
    $current_user_id = $_SESSION['id_u'] ?? null;

    // Récupération des informations utilisateur
    $stmt = $bdd->prepare("SELECT * FROM users WHERE id_u = ?");
    $stmt->execute([$profile_id]);
    $user = $stmt->fetch();

    if ($user) { ?>
        <div class="profile-container">
            <!-- Messages d'alerte -->
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- En-tête du profil -->
            <div class="profile-header">
                <img src="../assets/images/Profil/profil_<?= $_GET['id'] ?>" 
                     class="profile-avatar"
                     alt="Avatar de <?= htmlspecialchars($user['login']) ?>">
                <h1><?= htmlspecialchars($user['login']) ?></h1>
                
                <!-- Bouton Ajouter ami -->
<?php if ($current_user_id && $current_user_id != $profile_id): 
    // Vérification complète du statut d'amitié
    $checkFriend = $bdd->prepare("SELECT statut FROM amis 
        WHERE (utilisateur_id = ? AND ami_id = ?)
        OR (utilisateur_id = ? AND ami_id = ?)
        AND statut = 'accepte'");
    $checkFriend->execute([$current_user_id, $profile_id, $profile_id, $current_user_id]);
    
    if ($checkFriend->rowCount() === 0): 
        // Vérifier s'il y a déjà une demande en attente
        $checkRequest = $bdd->prepare("SELECT id FROM amis 
            WHERE utilisateur_id = ? 
            AND ami_id = ? 
            AND statut = 'en_attente'");
        $checkRequest->execute([$current_user_id, $profile_id]);
        ?>
        <div class="friend-actions">
            <?php if ($checkRequest->rowCount() === 0): ?>
                <form method="POST" action="friend_request.php">
                    <input type="hidden" name="ami_id" value="<?= $profile_id ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Ajouter en ami
                    </button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary" disabled>
                    <i class="fas fa-clock"></i> Demande envoyée
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
            </div>

            <!-- Informations du profil -->
            <div class="profile-info">
                <div class="info-item">
                    <span class="label">Email :</span>
                    <span class="value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="label">Âge :</span>
                    <span class="value"><?= $user['age'] ? htmlspecialchars($user['age']) . ' ans' : 'Non renseigné' ?></span>
                </div>
                
                <div class="info-item">
                    <span class="label">Langue :</span>
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
                    <span class="label">Pays :</span>
                    <span class="value"><?= htmlspecialchars($user['pays'] ?? 'Non renseigné') ?></span>
                </div>
            </div>

            <!-- Formulaire de mise à jour (visible seulement pour le propriétaire) -->
            <?php if ($current_user_id === $profile_id): ?>
                <div class="update-form mt-5">
                    <h2><i class="fas fa-edit"></i> Modifier mon profil</h2>
                    
                    <form method="POST">
    <div class="form-group">
        <label>Email :</label>
        <input type="email" name="email" 
               value="<?= htmlspecialchars($user['email']) ?>" 
               class="form-control">
    </div>
    
    <div class="form-group">
        <label>Âge :</label>
        <input type="number" name="age" 
       value="<?= htmlspecialchars($user['age'] ?? '') ?>" 
       class="form-control" min="0" max="120">
    </div>
                        
                        <div class="form-group">
                            <label>Langue :</label>
                            <select name="langue" class="form-control">
                                <option value="fr" <?= $user['langue'] === 'fr' ? 'selected' : '' ?>>Français</option>
                                <option value="en" <?= $user['langue'] === 'en' ? 'selected' : '' ?>>Anglais</option>
                                <option value="es" <?= $user['langue'] === 'es' ? 'selected' : '' ?>>Espagnol</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Pays :</label>
                            <input type="text" id="pays" name="pays" 
       value="<?= htmlspecialchars($user['pays'] ?? '') ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-success mt-3">
                            <i class="fas fa-save"></i> Sauvegarder
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php
    } else {
        echo "<div class='alert alert-danger'>Utilisateur introuvable</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID de profil invalide</div>";
}

include "../includes/footer.php";
?>