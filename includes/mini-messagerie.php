<?php
// Calculer le nombre de messages non lus
$nb_non_lus = 0;
if (isset($_SESSION['id_u'])) {
    $user_id = $_SESSION['id_u'];
    $req = $bdd->prepare("SELECT COUNT(*) FROM envoyer WHERE id_recept = :uid AND lu = 0");
    $req->execute(['uid' => $user_id]);
    $nb_non_lus = $req->fetchColumn();
}
?>

<!-- Style intégré -->
<style>
#chatButton {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 1050;
    width: 50px;
    height: 50px;
    padding: 0;
    border-radius: 50%;
    background-color: rgba(0, 0, 0);
    border: 1px solid #00FF00;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease;
}

#chatButton:hover {
    background-color:rgba(0, 0, 0, 0);
}

#chatButton i {
    font-size: 20px;
}

#chatButton .badge {
    position: absolute;
    top: -5px;
    left: 100%;
    transform: translate(-30%, -40%);
    background-color: #dc3545;
    color: white;
    padding: 4px 7px;
    font-size: 12px;
    border-radius: 50px;
}

#chatPanel {
    position: fixed;
    bottom: 65px;
    left: 65px;
    width: 250px;
    max-height: 420px;
    overflow-y: auto;
    z-index: 1040;
    border-radius: 0px;
    background: black;
    display: none;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
.slideIn {
    animation: slideIn 0.3s ease-out;
}

#chatPanel .header {
    padding-left: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#chatPanel .conversation {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
#chatPanel .conversation:hover {
    background-color: #00ff0055;
}
#chatPanel .conversation img {
    border-radius: 50%;
    margin-right: 10px;
    width: 40px;
    height: 40px;
    object-fit: cover;
}
#chatPanel .conversation .flex-grow-1 {
    min-width: 0;
}
#chatPanel .conversation .fw-bold {
    font-weight: 600;
    font-size: 16px;
}
#chatPanel .conversation .text-muted {
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
#chatPanel .conversation .badge {
    font-size: 0.75rem;
    padding: 5px 7px;
    background-color: #dc3545;
    color: white;
}
#chatPanel .header button {
    background-color: transparent;
    border: none;
    color: white;
    font-size: 16px;
    padding: 10px;
    cursor: pointer;
    transition: color 0.3s ease;
}
#chatPanel .header button:hover {
    color: #00FF00;
}
</style>

<!-- Bouton -->
<button id="chatButton" onclick="toggleChat()">
    <i class="fas fa-envelope fa-lg <?= $nb_non_lus > 0 ? 'text-danger' : '' ?>"></i>
    <?php if ($nb_non_lus > 0): ?>
        <span class="badge"><?= $nb_non_lus ?></span>
    <?php endif; ?>
</button>

<!-- Panneau Messagerie -->
<div id="chatPanel">
    <div class="header">
        <h6 class="mb-0 text-white">Messagerie</h6>
        <button onclick="toggleChat()">
            <i class="fas fa-times"></i>
        </button>
    </div>

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
                <div class="conversation" onclick="window.location.href='/metaforg/pages/messages.php?contact=<?= $conv['id_u'] ?>'">
                    <img src="/metaforg/assets/images/profil/profil_<?= $conv['id_u'] ?>" 
                         alt="Avatar de <?= htmlspecialchars($conv['login']) ?>" 
                         class="rounded-circle me-2" width="40" loading="lazy"
                         onerror="this.src='/metaforg/assets/images/profil/default.png';">
                    <div class="flex-grow-1">
                        <div class="fw-bold"><?= htmlspecialchars($conv['login']) ?></div>
                        <div class="text-muted"><?= htmlspecialchars($conv['message']) ?></div>
                    </div>
                    <?php if ($conv['non_lus'] > 0): ?>
                        <span class="badge rounded-pill"><?= $conv['non_lus'] ?></span>
                    <?php endif; ?>
                </div>
        <?php
            endforeach;
        }
        ?>
    </div>
</div>

<!-- Script -->
<script>
function toggleChat() {
    const panel = document.getElementById('chatPanel');
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';

    if (!isVisible) {
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

document.addEventListener('DOMContentLoaded', function () {
    scrollToBottom();
});

setInterval(() => {
    location.reload();
}, 30000);
</script>
