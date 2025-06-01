<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id_u'];
$search_results = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_term = '%' . $_GET['q'] . '%';

    // Recherche dans la base de données
    $req_search = $bdd->prepare("
        SELECT id_u, login
        FROM users
        WHERE login LIKE :search_term AND id_u != :user_id
        LIMIT 10
    ");
    $req_search->execute([
        'search_term' => $search_term,
        'user_id' => $user_id
    ]);
    $search_results = $req_search->fetchAll(PDO::FETCH_ASSOC);
    
    // Redirection si appelé depuis la messagerie
 // Redirection si appelé depuis la messagerie
if (isset($_GET['from_messages'])) {
    if (!empty($search_results)) {  // <-- Correction ici (parenthèse en moins)
        header("Location: messages.php?contact=".$search_results[0]['id_u']);
        exit;
    }
}
}

// Retour JSON pour les requêtes AJAX
if (isset($_GET['ajax'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    
    if (!empty($search_results)) {
        foreach ($search_results as &$result) {
            $result['avatar'] = file_exists("../assets/images/profil/profil_".$result['id_u']) ? 
                "../assets/images/profil/profil_".$result['id_u'] : 
                "../assets/images/profil/default.png";
        }
    }
    
    echo json_encode($search_results);
    exit;
}
?>

<div class="custom-container" id="search-section" style="max-width: 900px; margin: 40px auto 0 auto; padding-top: 40px;">
    <div class="user-card shadow-sm" id="search-user">
        <div class="card-content p-0">
            <div class="search-wrapper p-3">
                <!-- Formulaire de recherche -->
                <form method="GET" class="search-form mb-3">
                    <div class="input-group-custom">
                        <input
                            type="text"
                            name="q"
                            class="search-input"
                            placeholder="Rechercher un utilisateur..."
                            value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                            required="required"
                        >
                        <button class="search-btn" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26px" viewBox="0 0 64 64" class="search-icon">
                                <path d="M 28.300781 10.800781 C 17.100781 10.800781 7.9003906 20.000391 7.9003906 31.400391 C 7.9003906 42.800391 17.000781 52 28.300781 52 C 33.500781 52 38.200781 50.000781 41.800781 46.800781 L 43 48 L 41.900391 49.099609 C 41.300391 49.699609 41.300391 50.700781 41.900391 51.300781 L 50.900391 60.400391 C 51.200391 60.700391 51.6 60.900391 52 60.900391 C 52.4 60.900391 52.8 60.800391 53 60.400391 L 57 56.400391 C 57.5 55.700391 57.500391 54.799219 56.900391 54.199219 L 47.900391 45.099609 C 47.600391 44.799609 47.200781 44.699219 46.800781 44.699219 C 46.400781 44.699219 46.000781 44.799609 45.800781 45.099609 L 44.800781 46.199219 L 43.599609 45 C 46.799609 41.3 48.699219 36.600391 48.699219 31.400391 C 48.699219 20.000391 39.500781 10.800781 28.300781 10.800781 z M 28.300781 13.900391 C 37.900781 13.900391 45.699219 21.8 45.699219 31.5 C 45.699219 41.2 37.900781 49 28.300781 49 C 18.700781 49 10.900391 41.2 10.900391 31.5 C 10.900391 21.8 18.700781 13.900391 28.300781 13.900391 z M 28.400391 20.099609 C 23.600391 20.099609 19.400781 23.099609 17.800781 27.599609 C 17.500781 28.299609 17.9 29.100781 18.5 29.300781 C 18.6 29.300781 18.8 29.400391 19 29.400391 C 19.5 29.400391 20.000781 29 20.300781 28.5 C 21.500781 25 24.800391 22.699219 28.400391 22.699219 C 29.100391 22.699219 29.699219 22.100391 29.699219 21.400391 C 29.699219 20.700391 29.100391 20.099609 28.400391 20.099609 z M 18.900391 32.5 C 18.200391 32.5 17.599609 33.000781 17.599609 33.800781 L 17.599609 34 C 17.599609 34.7 18.100391 35.300781 18.900391 35.300781 C 19.600391 35.300781 20.199219 34.7 20.199219 34 L 20.199219 33.800781 C 20.199219 33.000781 19.700391 32.5 18.900391 32.5 z M 46.900391 48.300781 L 53.800781 55.300781 L 51.900391 57.199219 L 45 50.199219 L 46.900391 48.300781 z"></path>
                            </svg>
                        </button>
                    </div>
                    <?php if (isset($_GET['from_messages'])): ?>
                        <input type="hidden" name="from_messages" value="1">
                    <?php endif; ?>
                </form>

                <!-- Affichage des résultats de la recherche -->
                <?php if (!empty($search_results)): ?>
                    <div class="notifications-list" id="results-list">
                        <h2 class="card-title">Résultats de la recherche:</h2>
                        <?php foreach ($search_results as $result): ?>
                            <div class="result-item p-2 border rounded mb-2" 
                                 style="cursor:pointer"
                                 onclick="window.location.href='<?= isset($_GET['from_messages']) ? 'messages.php?contact='.$result['id_u'] : 'profil.php?id='.$result['id_u'] ?>'">
                                <div class="d-flex align-items-center">
                                    <img
                                        src="../assets/images/profil/profil_<?= $result['id_u'] ?>"
                                        alt="Avatar de <?= htmlspecialchars($result['login']) ?>"
                                        class="avatar rounded-circle me-2"
                                        width="40"
                                        height="40"
                                        loading="lazy"
                                        onerror="this.src='../assets/images/profil/default.png';">

                                    <div class="username fw-bold"><?= htmlspecialchars($result['login']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (isset($_GET['q'])): ?>
                    <div class="text-center text-muted mt-3">
                        <p>Aucun utilisateur trouvé.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php ob_end_flush(); ?>
<?php include "../includes/mini-messagerie.php";
include '../includes/footer.php';
?>