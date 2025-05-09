<?php
// Vérifier si la table publications existe, sinon la créer
try {
    // Vérifier l'existence de la table publications
    $checkTable = $bdd->query("SHOW TABLES LIKE 'publications'");
    if ($checkTable->rowCount() == 0) {
        // La table n'existe pas, on la crée
        $bdd->exec("CREATE TABLE `publications` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `contenu` text NOT NULL,
            `date` datetime NOT NULL DEFAULT current_timestamp(),
            `image` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_u`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
        
        // Insérer quelques publications d'exemple
        $bdd->exec("INSERT INTO publications (user_id, contenu) VALUES 
            (1, 'Première publication de test sur GamingHub !'),
            (1, 'Bienvenue sur notre nouvelle plateforme dédiée aux gamers !')");
    }
} catch (PDOException $e) {
    // En cas d'erreur lors de la création de la table
    error_log("Erreur de création de table: " . $e->getMessage());
}

// Pagination
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    $req = $bdd->prepare("SELECT p.*, u.login FROM publications p JOIN users u ON p.user_id = u.id_u ORDER BY p.date DESC LIMIT :limit OFFSET :offset");
    $req->bindValue(':limit', $limit, PDO::PARAM_INT);
    $req->bindValue(':offset', $offset, PDO::PARAM_INT);
    $req->execute();
    $posts = $req->fetchAll(PDO::FETCH_ASSOC);

    if ($posts === false) {
        $posts = [];
    }
} catch (PDOException $e) {
    // En cas d'erreur lors de la requête
    error_log("Erreur de requête: " . $e->getMessage());
    $posts = [];
}
?>

<div class="container">
    
<div id="fil-actualite">
    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <div class="publication-card mb-4 p-3 border rounded shadow-sm">
                <div class="post-header mb-2">
                    <strong><?= htmlspecialchars($post['login']) ?></strong>
                </div>
                <div class="post-content mb-2">
                    <?= nl2br(htmlspecialchars($post['message'] ?? 'Aucun message disponible')) ?>
                </div>
                <?php if (!empty($post['image'])): ?>
                    <div class="post-image">
                        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Image de publication" class="img-fluid rounded">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-posts text-muted">Aucune publication trouvée.</p>
    <?php endif; ?>
</div>


    <!-- Pagination -->
    <div class="pagination">
        <?php
        $total = $bdd->query("SELECT COUNT(*) FROM publications")->fetchColumn();
        $pages = ceil($total / $limit);
        
        for ($i = 1; $i <= $pages; $i++): 
            $active = ($i == $page) ? 'active' : '';
        ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $active ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <!-- Actualités Gaming -->
    <div id="rss-news">
        <h3 class="news-section-title">🎮 Dernières actualités des sites gaming</h3>
        
        <div class="news-source">
            <h4>ActuGaming</h4>
            <div id="actugaming-list" class="news-list"></div>
        </div>
        
        <div class="news-source">
            <h4>JVFrance</h4>
            <div id="jvfrance-list" class="news-list"></div>
        </div>
    </div>
</div>

<script>
    // Fonction générique pour charger les actualités
    function loadRSS(feedUrl, containerId) {
        fetch(`https://api.rss2json.com/v1/api.json?rss_url=${encodeURIComponent(feedUrl)}`)
            .then(response => response.json())
            .then(data => {
                let html = '<ul>';
                (data.items || []).slice(0, 5).forEach(item => {
                    html += `<li class="news-item">
                        <a href="${item.link}" target="_blank" class="news-link">
                            ${item.title}
                        </a>
                    </li>`;
                });
                html += '</ul>';
                document.getElementById(containerId).innerHTML = html;
            })
            .catch(() => {
                document.getElementById(containerId).innerHTML = 
                    "<p class='error-message'>Impossible de récupérer les actualités.</p>";
            });
    }

    // Charger les deux flux
    loadRSS('https://www.actugaming.net/feed/', 'actugaming-list');
    loadRSS('https://www.jvfrance.com/feed/', 'jvfrance-list');
</script>
