
<!-- Bouton Messagerie -->
<button
    id="chatButton"
    onclick="toggleChat()"
    class="btn btn-primary position-fixed d-flex align-items-center"
    style="bottom: 20px; left: 20px; z-index: 1050; padding: 10px 20px; border-radius: 8px;">
    <i class="fas fa-comment-alt me-2"></i>
    Messagerie
</button>

<!-- Panneau Messagerie -->
<div
    id="chatPanel"
    class="position-fixed bg-white shadow"
    style="bottom: 70px; left: 20px; width: 300px; max-height: 400px; overflow-y: auto; display: none; z-index: 1040; border-radius: 10px;">
    
    <!-- En-tête -->
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Messagerie</h6>
        <button class="btn btn-sm btn-secondary" onclick="toggleChat()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Contenu de la messagerie -->
    <div class="p-2" id="chatContent">
        <?php
        if (!isset($_SESSION['id_u'])) {
            echo "<div class='p-2 text-muted'>Non connecté</div>";
        } else {
            $user_id = $_SESSION['id_u'];

            $req = $bdd->prepare("
                SELECT 
                    u.id_u, u.login, u.photo_profil,
                    e.message, MAX(e.date_env) as last_date,
                    SUM(CASE WHEN e.lu = 0 AND e.id_recept = :user_id THEN 1 ELSE 0 END) as non_lus
                FROM envoyer e
                JOIN users u ON u.id_u = CASE 
                    WHEN e.id_exp = :user_id THEN e.id_recept 
                    ELSE e.id_exp 
                END
                WHERE e.id_exp = :user_id OR e.id_recept = :user_id
                GROUP BY u.id_u, u.login, u.photo_profil
                ORDER BY last_date DESC
                LIMIT 5
            ");
            $req->execute(['user_id' => $user_id]);
            $conversations = $req->fetchAll(PDO::FETCH_ASSOC);

            foreach ($conversations as $conv):
            ?>
                <div class="d-flex align-items-center p-2 border-bottom" onclick="window.location.href='/metaforg/pages/messages.php?contact=<?= $conv['id_u'] ?>'" style="cursor: pointer;">
                    <img src="<?= $conv['photo_profil'] ?? 'assets/images/default_avatar.png' ?>" width="40" class="rounded-circle me-2">
                    <div class="flex-grow-1">
                        <div class="fw-bold"><?= htmlspecialchars($conv['login']) ?></div>
                        <div class="small text-muted text-truncate"><?= htmlspecialchars($conv['message']) ?></div>
                    </div>
                    <?php if ($conv['non_lus'] > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?= $conv['non_lus'] ?></span>
                    <?php endif; ?>
                </div>
            <?php
            endforeach;
        }
        ?>
    </div>
</div>

<!-- JavaScript -->
<script>
function toggleChat() {
    const panel = document.getElementById('chatPanel');
    const isVisible = panel.style.display !== 'none';

    if (isVisible) {
        panel.style.display = 'none';
    } else {
        panel.style.display = 'block';
        panel.classList.add('slideIn');
        setTimeout(() => panel.classList.remove('slideIn'), 300);
        scrollToBottom();
    }
}

function scrollToBottom() {
    const panel = document.getElementById('chatContent');
    if (panel) {
        panel.scrollTop = panel.scrollHeight;
    }
}

// Fermer si clic en dehors
document.addEventListener('click', function (event) {
    const chatPanel = document.getElementById('chatPanel');
    const chatButton = document.getElementById('chatButton');

    if (
        chatPanel &&
        !chatPanel.contains(event.target) &&
        chatButton &&
        !chatButton.contains(event.target)
    ) {
        chatPanel.style.display = 'none';
    }
});

// Auto-scroll au chargement
document.addEventListener('DOMContentLoaded', function () {
    scrollToBottom();
});

// Rechargement automatique des messages toutes les 30 secondes
setInterval(() => {
    location.reload();
}, 30000);
</script>

<!-- Animation optionnelle -->
<style>
@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.slideIn {
    animation: slideIn 0.3s ease-out;
}
</style>
