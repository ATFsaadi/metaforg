<?php
/**
 * Profil unifié - Combine les fonctionnalités de profil.php et profile.php
 * Ce fichier permet à la fois de voir son propre profil (édition) et de voir le profil des autres
 */
session_start();

include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_u'])) {
    header("Location: ../index.php");
    exit;
}

// Déterminer si c'est le profil de l'utilisateur connecté ou celui d'un autre utilisateur
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id_u'];
$is_own_profile = ($profile_id == $_SESSION['id_u']);

// Récupérer les informations du profil
$reqProfil = $bdd->prepare("SELECT * FROM users WHERE id_u = ?");
$reqProfil->execute([$profile_id]);
$userProfile = $reqProfil->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'existe pas, rediriger
if (!$userProfile) {
    header('Location: ../home.php?error=Utilisateur introuvable');
    exit;
}

// Traitement de la mise à jour du profil (si c'est le profil de l'utilisateur connecté)
$error = '';
$success = '';

if ($is_own_profile && isset($_POST['submit'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    
    // Vérifier si le mot de passe a été fourni
    if (!empty($_POST['mdp'])) {
        // Vérifier que les mots de passe correspondent
        if ($_POST['mdp'] !== $_POST['mdpConfirm']) {
            $error = "Les mots de passe ne correspondent pas";
        } else {
            $mdp = sha1($_POST['mdp']);
            
            // Mise à jour avec mot de passe
            $update = $bdd->prepare("UPDATE users SET login = ?, email = ?, mdp = ? WHERE id_u = ?");
            $update->execute([$login, $email, $mdp, $_SESSION['id_u']]);
            $success = "Profil mis à jour avec succès (mot de passe inclus)";
        }
    } else {
        // Mise à jour sans mot de passe
        $update = $bdd->prepare("UPDATE users SET login = ?, email = ? WHERE id_u = ?");
        $update->execute([$login, $email, $_SESSION['id_u']]);
        $success = "Profil mis à jour avec succès";
    }
    
    // Recharger les informations du profil après la mise à jour
    if (empty($error)) {
        $reqProfil->execute([$profile_id]);
        $userProfile = $reqProfil->fetch(PDO::FETCH_ASSOC);
    }
}

// Récupérer les publications de l'utilisateur
$reqPublications = $bdd->prepare("
    SELECT i.id_img, i.nom, i.chemin, i.date_img, i.base64_data 
    FROM images i 
    WHERE i.u_id = ? 
    ORDER BY i.date_img DESC 
    LIMIT 10
");
$reqPublications->execute([$profile_id]);
$publications = $reqPublications->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur courant est ami avec le profil consulté
$is_friend = false;
if (!$is_own_profile) {
    $checkFriend = $bdd->prepare("
        SELECT * FROM amis 
        WHERE (id_demandeur = ? AND id_receveur = ?) 
        OR (id_demandeur = ? AND id_receveur = ?)
    ");
    $checkFriend->execute([$_SESSION['id_u'], $profile_id, $profile_id, $_SESSION['id_u']]);
    $is_friend = ($checkFriend->rowCount() > 0);
}

// Obtenir le niveau d'utilisateur
$user_level = $userProfile['lvl'] ?? 1;
$niveau_texte = "";
switch ($user_level) {
    case 1:
        $niveau_texte = "Novice";
        break;
    case 2:
        $niveau_texte = "Membre";
        break;
    case 3:
        $niveau_texte = "Modérateur";
        break;
    case 4:
        $niveau_texte = "Administrateur";
        break;
    default:
        $niveau_texte = "Inconnu";
}
?>

<div class="container mt-5 pt-3">
    <div class="row">
        <!-- Colonne de gauche: informations du profil -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-dark text-light">
                    <h4 class="mb-0"><?= htmlspecialchars($userProfile['login']) ?></h4>
                </div>
                
                <div class="card-body text-center">
                    <img 
                        src="../avatar.php?id=<?= $profile_id ?>" 
                        class="rounded-circle mb-3" 
                        width="150" 
                        height="150" 
                        alt="Avatar de <?= htmlspecialchars($userProfile['login']) ?>"
                    >
                    
                    <h5 class="mb-3" style="color: #00FF00;">
                        <?= htmlspecialchars($niveau_texte) ?>
                    </h5>
                    
                    <p class="mb-2">
                        <i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($userProfile['email']) ?>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-calendar me-2"></i> Membre depuis: 
                        <?= date('d/m/Y', strtotime($userProfile['date_u'] ?? 'now')) ?>
                    </p>
                    
                    <?php if (!$is_own_profile): ?>
                        <div class="mt-4">
                            <?php if ($is_friend): ?>
                                <button class="btn btn-success" disabled>
                                    <i class="fas fa-check me-2"></i> Ami
                                </button>
                            <?php else: ?>
                                <form method="POST" action="amis-unified.php" class="d-inline">
                                    <input type="hidden" name="friend_id" value="<?= $profile_id ?>">
                                    <input type="hidden" name="redirect" value="1">
                                    <button type="submit" class="btn btn-outline-success">
                                        <i class="fas fa-user-plus me-2"></i> Ajouter en ami
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="messages.php?user=<?= $profile_id ?>" class="btn btn-outline-primary ms-2">
                                <i class="fas fa-envelope me-2"></i> Message
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($is_own_profile): ?>
                <!-- Formulaire de modification du profil -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-dark text-light">
                        <h5 class="mb-0">Modifier votre profil</h5>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="login" class="form-label">Nom d'utilisateur</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="login" 
                                    name="login" 
                                    value="<?= htmlspecialchars($userProfile['login']) ?>" 
                                    required
                                >
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    value="<?= htmlspecialchars($userProfile['email']) ?>" 
                                    required
                                >
                            </div>
                            
                            <div class="mb-3">
                                <label for="mdp" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="mdp" 
                                    name="mdp"
                                >
                            </div>
                            
                            <div class="mb-3">
                                <label for="mdpConfirm" class="form-label">Confirmez le mot de passe</label>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="mdpConfirm" 
                                    name="mdpConfirm"
                                >
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-success w-100">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Colonne de droite: publications -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Publications</h4>
                    
                    <?php if ($is_own_profile): ?>
                        <a href="poster.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-2"></i> Nouvelle publication
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <?php if (empty($publications)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= $is_own_profile ? "Vous n'avez pas encore de publications." : "Cet utilisateur n'a pas encore de publications." ?>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($publications as $pub): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <?php if (!empty($pub['base64_data'])): ?>
                                            <img 
                                                src="<?= htmlspecialchars($pub['base64_data']) ?>" 
                                                class="card-img-top" 
                                                alt="<?= htmlspecialchars($pub['nom']) ?>"
                                                style="height: 200px; object-fit: cover;"
                                            >
                                        <?php elseif (!empty($pub['chemin'])): ?>
                                            <img 
                                                src="../ImgU/<?= basename($pub['chemin']) ?>" 
                                                class="card-img-top" 
                                                alt="<?= htmlspecialchars($pub['nom']) ?>"
                                                style="height: 200px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='../avatar/avatar_1.png';"
                                            >
                                        <?php endif; ?>
                                        
                                        <div class="card-body">
                                            <h5 class="card-title" style="color: #00FF00;">
                                                <?= htmlspecialchars($pub['nom']) ?>
                                            </h5>
                                            <p class="card-text text-muted">
                                                <small>
                                                    <i class="far fa-clock me-1"></i> 
                                                    <?= date('d/m/Y H:i', strtotime($pub['date_img'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                        
                                        <div class="card-footer bg-light border-0">
                                            <a href="../home.php?post=<?= $pub['id_img'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> Voir
                                            </a>
                                            
                                            <?php if ($is_own_profile): ?>
                                                <button 
                                                    class="btn btn-sm btn-outline-danger float-end delete-post" 
                                                    data-post-id="<?= $pub['id_img'] ?>"
                                                >
                                                    <i class="fas fa-trash me-1"></i> Supprimer
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour la suppression de publication -->
<?php if ($is_own_profile): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-post');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')) {
                // Envoyer une requête AJAX pour supprimer la publication
                fetch('../includes/delete_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'post_id=' + postId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page après suppression
                        window.location.reload();
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la suppression');
                });
            }
        });
    });
});
</script>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>
