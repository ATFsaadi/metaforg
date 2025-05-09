<?php
session_start();
include "../includes/connexion.php";

// Vérifier si l'utilisateur est connecté et est admin (niveau supérieur à 3)
if (!isset($_SESSION['id_u']) || !isset($_SESSION['lvl']) || $_SESSION['lvl'] <= 3) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

// Traitement des actions d'administration
$success_message = "";
$error_message = "";

// 1. Gestion des utilisateurs
if (isset($_POST['action']) && $_POST['action'] == 'update_user_level') {
    $user_id = intval($_POST['user_id']);
    $new_level = intval($_POST['new_level']);
    
    try {
        $stmt = $bdd->prepare("UPDATE users SET lvl = ? WHERE id_u = ?");
        $stmt->execute([$new_level, $user_id]);
        $success_message = "Niveau de l'utilisateur mis à jour avec succès!";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la mise à jour: " . $e->getMessage();
    }
}

// 2. Suppression d'utilisateur
if (isset($_POST['action']) && $_POST['action'] == 'delete_user') {
    $user_id = intval($_POST['user_id']);
    
    // Ne pas permettre la suppression de son propre compte
    if ($user_id == $_SESSION['id_u']) {
        $error_message = "Vous ne pouvez pas supprimer votre propre compte!";
    } else {
        try {
            // Commencer une transaction
            $bdd->beginTransaction();
            
            // Supprimer toutes les publications de l'utilisateur
            $stmt = $bdd->prepare("DELETE FROM publications WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            // Supprimer toutes les images de l'utilisateur
            $stmt = $bdd->prepare("DELETE FROM images WHERE u_id = ?");
            $stmt->execute([$user_id]);
            
            // Supprimer l'utilisateur
            $stmt = $bdd->prepare("DELETE FROM users WHERE id_u = ?");
            $stmt->execute([$user_id]);
            
            // Valider la transaction
            $bdd->commit();
            
            $success_message = "Utilisateur supprimé avec succès!";
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $bdd->rollBack();
            $error_message = "Erreur lors de la suppression: " . $e->getMessage();
        }
    }
}

// 3. Suppression de publication
if (isset($_POST['action']) && $_POST['action'] == 'delete_publication') {
    $pub_id = intval($_POST['pub_id']);
    
    try {
        $stmt = $bdd->prepare("DELETE FROM publications WHERE id_p = ?");
        $stmt->execute([$pub_id]);
        $success_message = "Publication supprimée avec succès!";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression: " . $e->getMessage();
    }
}

// 4. Suppression d'image
if (isset($_POST['action']) && $_POST['action'] == 'delete_image') {
    $img_id = intval($_POST['img_id']);
    
    try {
        // Récupérer le chemin de l'image avant de la supprimer
        $stmt = $bdd->prepare("SELECT chemin FROM images WHERE id_img = ?");
        $stmt->execute([$img_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Supprimer le fichier physique si possible
            $image_path = '../' . $image['chemin'];
            if (file_exists($image_path)) {
                @unlink($image_path);
            }
            
            // Supprimer l'entrée de la base de données
            $stmt = $bdd->prepare("DELETE FROM images WHERE id_img = ?");
            $stmt->execute([$img_id]);
            $success_message = "Image supprimée avec succès!";
        } else {
            $error_message = "Image introuvable!";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression: " . $e->getMessage();
    }
}

// Récupérer des statistiques pour le tableau de bord
$stats = [];

try {
    // Nombre total d'utilisateurs
    $stmt = $bdd->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Nombre total de publications
    $stmt = $bdd->query("SELECT COUNT(*) FROM publications");
    $stats['total_publications'] = $stmt->fetchColumn();
    
    // Nombre total d'images
    $stmt = $bdd->query("SELECT COUNT(*) FROM images");
    $stats['total_images'] = $stmt->fetchColumn();
    
    // Utilisateurs récemment inscrits
    $stmt = $bdd->query("SELECT id_u, login, email, lvl FROM users ORDER BY id_u DESC LIMIT 10");
    $stats['recent_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Publications récentes
    $stmt = $bdd->query("SELECT p.id_p, p.contenu, p.date, u.login FROM publications p JOIN users u ON p.user_id = u.id_u ORDER BY p.date DESC LIMIT 10");
    $stats['recent_publications'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Images récentes
    $stmt = $bdd->query("SELECT i.id_img, i.nom, i.chemin, i.date_img, u.login FROM images i JOIN users u ON i.u_id = u.id_u ORDER BY i.date_img DESC LIMIT 10");
    $stats['recent_images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des statistiques: " . $e->getMessage();
}

include "../includes/header-PG.php";
?>

<!-- Page d'administration -->
<div class="container mt-5 pt-4">
    <h1 class="mb-4">Panneau d'administration</h1>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>
    
    <!-- Tableau de bord -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4"><?= $stats['total_users'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Publications</h5>
                    <p class="card-text display-4"><?= $stats['total_publications'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Images</h5>
                    <p class="card-text display-4"><?= $stats['total_images'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Onglets pour les différentes sections -->
    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="users-tab" data-bs-toggle="tab" href="#users" role="tab">Utilisateurs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="publications-tab" data-bs-toggle="tab" href="#publications" role="tab">Publications</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="images-tab" data-bs-toggle="tab" href="#images" role="tab">Images</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab">Paramètres</a>
        </li>
    </ul>
    
    <!-- Contenu des onglets -->
    <div class="tab-content" id="adminTabsContent">
        <!-- Onglet Utilisateurs -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <h3>Gestion des utilisateurs</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom d'utilisateur</th>
                            <th>Email</th>
                            <th>Niveau</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_users'] as $user): ?>
                        <tr>
                            <td><?= $user['id_u'] ?></td>
                            <td><?= htmlspecialchars($user['login']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="update_user_level">
                                    <input type="hidden" name="user_id" value="<?= $user['id_u'] ?>">
                                    <select name="new_level" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="0" <?= $user['lvl'] == 0 ? 'selected' : '' ?>>Utilisateur (0)</option>
                                        <option value="50" <?= $user['lvl'] == 50 ? 'selected' : '' ?>>Modérateur (50)</option>
                                        <option value="100" <?= $user['lvl'] == 100 ? 'selected' : '' ?>>Admin (100)</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="profil.php?id=<?= $user['id_u'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                
                                <form method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $user['id_u'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Onglet Publications -->
        <div class="tab-pane fade" id="publications" role="tabpanel">
            <h3>Gestion des publications</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Contenu</th>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['recent_publications'] as $pub): ?>
                        <tr>
                            <td><?= $pub['id_p'] ?></td>
                            <td><?= mb_substr(htmlspecialchars($pub['contenu']), 0, 50) . (strlen($pub['contenu']) > 50 ? '...' : '') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pub['date'])) ?></td>
                            <td><?= htmlspecialchars($pub['login']) ?></td>
                            <td>
                                <form method="post" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette publication?')">
                                    <input type="hidden" name="action" value="delete_publication">
                                    <input type="hidden" name="pub_id" value="<?= $pub['id_p'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Onglet Images -->
        <div class="tab-pane fade" id="images" role="tabpanel">
            <h3>Gestion des images</h3>
            <div class="row">
                <?php foreach ($stats['recent_images'] as $img): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="../<?= htmlspecialchars($img['chemin']) ?>" class="card-img-top" alt="Image" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars(mb_substr($img['nom'], 0, 20)) ?></h5>
                            <p class="card-text">Posté par: <?= htmlspecialchars($img['login']) ?></p>
                            <p class="card-text"><small class="text-muted"><?= date('d/m/Y', strtotime($img['date_img'])) ?></small></p>
                            <form method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette image?')">
                                <input type="hidden" name="action" value="delete_image">
                                <input type="hidden" name="img_id" value="<?= $img['id_img'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger w-100"><i class="fas fa-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Onglet Paramètres -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
            <h3>Paramètres du site</h3>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Maintenance du site</h5>
                </div>
                <div class="card-body">
                    <p>Ces actions sont irréversibles et doivent être utilisées avec précaution.</p>
                    
                    <div class="mb-3">
                        <h6>Nettoyage des images orphelines</h6>
                        <p>Supprime les images qui ne sont pas liées à une publication.</p>
                        <a href="maintenance.php?action=clean_orphan_images" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir nettoyer les images orphelines?')">Nettoyer les images orphelines</a>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Synchroniser les photos de profil</h6>
                        <p>Assure que toutes les photos de profil sont correctement indexées dans la base de données.</p>
                        <a href="../sync-profile-images.php" class="btn btn-info">Synchroniser les photos de profil</a>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Vérification des permissions</h6>
                        <p>Vérifie et corrige les permissions des dossiers d'uploads.</p>
                        <a href="maintenance.php?action=check_permissions" class="btn btn-secondary">Vérifier les permissions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript pour activer les onglets Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var triggerTabList = [].slice.call(document.querySelectorAll('#adminTabs a'))
        triggerTabList.forEach(function(triggerEl) {
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault()
                var tabTrigger = new bootstrap.Tab(triggerEl)
                tabTrigger.show()
            })
        })
    });
</script>

<?php include "../includes/footer.php"; ?>
