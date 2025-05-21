<?php
include "connexion.php";?>
   <!-- Messagerie -->
    <button
                    id="chatButton"
                    onclick="toggleChat()"
                    class="btn btn-primary position-fixed d-flex align-items-center"
                    style="bottom: 20px; left: 20px; z-index: 1050; padding: 10px 20px; border-radius: 8px;">
                    <i class="fas fa-comment-alt me-2"></i>
                    Messagerie
                </button>

                <div
                    id="chatPanel"
                    class="position-fixed bg-white shadow"
                    style="bottom: 70px; left: 20px; width: 300px; max-height: 400px; overflow-y: auto; display: none; z-index: 1040; border-radius: 10px;">
                    <div
                        class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Messagerie</h6>
                        <button class="btn btn-sm btn-secondary" onclick="toggleChat()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="d-flex align-items-center p-2 border-bottom">
                            <div class="position-relative">
                                <?php
                                    // Idéalement, nous devrions avoir un ID d'utilisateur réel ici
                                    // mais pour cet exemple, nous utilisons l'image par défaut
                                    $messageriePath = "assets/images/default_avatar.png";
                                ?>
                                <img
                                    src="<?= $messageriePath ?>"
                                    alt="Avatar Ami <?= $i ?>"
                                    class="rounded-circle"
                                    width="40"
                                    loading="lazy">
                                <span
                                    class="position-absolute bg-success rounded-circle"
                                    style="width: 10px; height: 10px; bottom: 3px; right: 3px; border: 2px solid white;"></span>
                            </div>
                            <div class="ms-2">
                                <div class="fw-bold">Ami
                                    <?= $i ?></div>
                                <div class="small text-muted">En ligne</div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <script>
// Fonction pour afficher / masquer le panneau de chat avec animation
function toggleChat() {
    const panel = document.getElementById('chatPanel');
    const isVisible = panel.style.display !== 'none';

    if (isVisible) {
        panel.style.display = 'none';
    } else {
        panel.style.display = 'block';
        panel.classList.add('slideIn');

        // Supprimer l'animation après qu'elle se soit jouée
        setTimeout(() => panel.classList.remove('slideIn'), 300);
        
        // Scroll automatiquement en bas des messages
        scrollToBottom();
    }
}

// Fonction pour défiler jusqu'en bas de la liste de messages
function scrollToBottom() {
    const messagesList = document.getElementById('messagesList');
    if (messagesList) {
        messagesList.scrollTop = messagesList.scrollHeight;
    }
}

// Fermer le panneau de chat si clic en dehors
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

// Scroll en bas automatiquement au chargement initial
document.addEventListener('DOMContentLoaded', function () {
    scrollToBottom();
});
</script>
