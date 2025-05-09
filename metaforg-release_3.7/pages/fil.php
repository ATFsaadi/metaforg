<?php
// Pagination
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Requête pour récupérer les publications
$req = $bdd->prepare("SELECT p.*, u.login FROM publications p JOIN users u ON p.user_id = u.id_u ORDER BY p.date DESC LIMIT :limit OFFSET :offset");
$req->bindValue(':limit', $limit, PDO::PARAM_INT);
$req->bindValue(':offset', $offset, PDO::PARAM_INT);
$req->execute();
$posts = $req->fetchAll(PDO::FETCH_ASSOC);

if ($posts === false) {
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
                        <?= nl2br(htmlspecialchars($post['contenu'] ?? 'Aucun message disponible')) ?>
                    </div>

                    <!-- Affichage de l'image si elle existe -->
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
        // Calcul du nombre total de pages
        $total = $bdd->query("SELECT COUNT(*) FROM publications")->fetchColumn();
        $pages = ceil($total / $limit);
        
        // Affichage des liens de pagination
        for ($i = 1; $i <= $pages; $i++): 
            $active = ($i == $page) ? 'active' : '';
        ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $active ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <!-- Actualités Gaming (optionnel) -->
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

    // Charger les deux flux RSS
    loadRSS('https://www.actugaming.net/feed/', 'actugaming-list');
    loadRSS('https://www.jvfrance.com/feed/', 'jvfrance-list');
</script>
