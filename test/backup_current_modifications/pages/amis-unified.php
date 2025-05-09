<?php
/**
 * Gestion des amis unifiée - Combine les fonctionnalités de amis.php, ajouter_ami.php et add_friend.php
 * Ce fichier permet de gérer l'affichage, l'ajout et la suppression d'amis
 */
session_start();

include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_u'])) {
    header("Location: ../index.php");
    exit;
}

// Action à effectuer (afficher, ajouter, supprimer)
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$response = ['success' => false, 'message' => ''];

// Traitement de l'ajout d'ami si soumis par formulaire
if (isset($_POST['friend_id'])) {
    $action = 'add';
    $friend_id = intval($_POST['friend_id']);
}

// Traitement des actions
switch ($action) {
    case 'add':
        // Ajouter un ami
        $friend_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['friend_id']) ? intval($_POST['friend_id']) : 0);
        
        if ($friend_id == 0) {
            $response = ['success' => false, 'message' => 'ID utilisateur invalide'];
        } elseif ($friend_id == $_SESSION['id_u']) {
            $response = ['success' => false, 'message' => 'Vous ne pouvez pas vous ajouter comme ami'];
        } else {
            // Vérifier si l'utilisateur existe
            $check_user = $bdd->prepare("SELECT id_u FROM users WHERE id_u = ?");
            $check_user->execute([$friend_id]);
            
            if ($check_user->rowCount() == 0) {
                $response = ['success' => false, 'message' => 'Utilisateur introuvable'];
            } else {
                // Vérifier si l'amitié existe déjà
                $check_friendship = $bdd->prepare("
                    SELECT * FROM amis 
                    WHERE (id_demandeur = ? AND id_receveur = ?) 
                    OR (id_demandeur = ? AND id_receveur = ?)
                ");
                $check_friendship->execute([$_SESSION['id_u'], $friend_id, $friend_id, $_SESSION['id_u']]);
                
                if ($check_friendship->rowCount() > 0) {
                    $response = ['success' => false, 'message' => 'Vous êtes déjà amis avec cet utilisateur'];
                } else {
                    // Ajouter l'amitié
                    $add_friend = $bdd->prepare("
                        INSERT INTO amis (id_demandeur, id_receveur, date_a) 
                        VALUES (?, ?, NOW())
                    ");
                    
                    if ($add_friend->execute([$_SESSION['id_u'], $friend_id])) {
                        $response = ['success' => true, 'message' => 'Ami ajouté avec succès'];
                    } else {
                        $response = ['success' => false, 'message' => 'Erreur lors de l\'ajout de l\'ami'];
                    }
                }
            }
        }
        
        // Si c'est une requête AJAX (depuis search.php par exemple)
        if (isset($_POST['friend_id']) && !isset($_POST['redirect'])) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        break;
        
    case 'remove':
        // Supprimer un ami
        $friend_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($friend_id == 0) {
            $response = ['success' => false, 'message' => 'ID utilisateur invalide'];
        } else {
            // Supprimer l'amitié
            $remove_friend = $bdd->prepare("
                DELETE FROM amis 
                WHERE (id_demandeur = ? AND id_receveur = ?) 
                OR (id_demandeur = ? AND id_receveur = ?)
            ");
            
            if ($remove_friend->execute([$_SESSION['id_u'], $friend_id, $friend_id, $_SESSION['id_u']])) {
                $response = ['success' => true, 'message' => 'Ami supprimé avec succès'];
            } else {
                $response = ['success' => false, 'message' => 'Erreur lors de la suppression de l\'ami'];
            }
        }
        break;
}

// Récupérer la liste des amis (pour l'affichage par défaut)
$friends = [];

if ($action == 'list' || isset($_POST['redirect'])) {
    $query_friends = $bdd->prepare("
        SELECT u.id_u, u.login, u.email, u.lvl, a.date_a
        FROM amis a
        JOIN users u ON (a.id_receveur = u.id_u OR a.id_demandeur = u.id_u)
        WHERE (a.id_demandeur = ? AND u.id_u != ?) OR (a.id_receveur = ? AND u.id_u != ?)
        ORDER BY a.date_a DESC
    ");
    $query_friends->execute([$_SESSION['id_u'], $_SESSION['id_u'], $_SESSION['id_u'], $_SESSION['id_u']]);
    $friends = $query_friends->fetchAll(PDO::FETCH_ASSOC);
}

// Si c'est une action qui nécessite une redirection et que la redirection est demandée
if (in_array($action, ['add', 'remove']) && (isset($_POST['redirect']) || isset($_GET['redirect']))) {
    header("Location: amis-unified.php?status=" . ($response['success'] ? 'success' : 'error') . "&message=" . urlencode($response['message']));
    exit;
}

// Gestion des messages de statut
$status_class = '';
$status_message = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $status_class = 'alert-success';
    } else {
        $status_class = 'alert-danger';
    }
    
    $status_message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
}
?>

<div class="container mt-5 pt-3">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-dark text-light d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i> Mes amis</h4>
                    
                    <a href="search-unified.php?type=users" class="btn btn-success btn-sm">
                        <i class="fas fa-user-plus me-2"></i> Trouver des amis
                    </a>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($status_message)): ?>
                        <div class="alert <?= $status_class ?>"><?= htmlspecialchars($status_message) ?></div>
                    <?php endif; ?>
                    
                    <?php if (empty($friends)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Vous n'avez pas encore d'amis. Utilisez la recherche pour en trouver !
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($friends as $friend): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img 
                                            src="../avatar.php?id=<?= $friend['id_u'] ?>" 
                                            class="rounded-circle me-3" 
                                            width="50" 
                                            height="50" 
                                            alt="Avatar de <?= htmlspecialchars($friend['login']) ?>"
                                        >
                                        
                                        <div>
                                            <h5 class="mb-1" style="color: #00FF00;">
                                                <?= htmlspecialchars($friend['login']) ?>
                                            </h5>
                                            <p class="mb-1 text-muted">
                                                <small>Ami depuis <?= date('d/m/Y', strtotime($friend['date_a'])) ?></small>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <a href="profil-unified.php?id=<?= $friend['id_u'] ?>" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-user me-1"></i> Profil
                                        </a>
                                        
                                        <a href="messages.php?user=<?= $friend['id_u'] ?>" class="btn btn-sm btn-outline-success me-2">
                                            <i class="fas fa-envelope me-1"></i> Message
                                        </a>
                                        
                                        <a 
                                            href="amis-unified.php?action=remove&id=<?= $friend['id_u'] ?>&redirect=1"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet ami ?');"
                                        >
                                            <i class="fas fa-user-minus me-1"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Suggestions d'amis -->
            <div class="card shadow mt-4">
                <div class="card-header bg-dark text-light">
                    <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i> Suggestions d'amis</h5>
                </div>
                
                <div class="card-body">
                    <?php
                    // Récupérer les suggestions (utilisateurs qui ne sont pas déjà amis)
                    $query_suggestions = $bdd->prepare("
                        SELECT u.id_u, u.login, u.email
                        FROM users u
                        WHERE u.id_u != ?
                        AND u.id_u NOT IN (
                            SELECT IF(a.id_demandeur = ?, a.id_receveur, a.id_demandeur)
                            FROM amis a
                            WHERE a.id_demandeur = ? OR a.id_receveur = ?
                        )
                        ORDER BY RAND()
                        LIMIT 5
                    ");
                    $query_suggestions->execute([$_SESSION['id_u'], $_SESSION['id_u'], $_SESSION['id_u'], $_SESSION['id_u']]);
                    $suggestions = $query_suggestions->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <?php if (empty($suggestions)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Aucune suggestion d'ami disponible pour le moment.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($suggestions as $suggestion): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex align-items-center">
                                            <img 
                                                src="../avatar.php?id=<?= $suggestion['id_u'] ?>" 
                                                class="rounded-circle me-3" 
                                                width="50" 
                                                height="50" 
                                                alt="Avatar"
                                            >
                                            
                                            <div>
                                                <h5 class="mb-1" style="color: #00FF00;">
                                                    <?= htmlspecialchars($suggestion['login']) ?>
                                                </h5>
                                                
                                                <form method="POST" action="amis-unified.php" class="mt-2">
                                                    <input type="hidden" name="friend_id" value="<?= $suggestion['id_u'] ?>">
                                                    <input type="hidden" name="redirect" value="1">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-user-plus me-1"></i> Ajouter
                                                    </button>
                                                    
                                                    <a href="profil-unified.php?id=<?= $suggestion['id_u'] ?>" class="btn btn-sm btn-outline-primary ms-1">
                                                        <i class="fas fa-user me-1"></i> Voir
                                                    </a>
                                                </form>
                                            </div>
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

<?php include "../includes/footer.php"; ?>
