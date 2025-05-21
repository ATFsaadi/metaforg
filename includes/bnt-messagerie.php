<?php include "connexion.php"; ?>
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
    
    <!-- Contenu AJAX -->
    <div class="p-2" id="chatContent">
        Chargement...
    </div>
</div>

<!-- JavaScript -->
<script>
function toggleChat() {
    const panel = document.getElementById('chatPanel');
    const content = document.getElementById('chatContent');
    const isVisible = panel.style.display !== 'none';

    if (isVisible) {
        panel.style.display = 'none';
    } else {
        panel.style.display = 'block';
        panel.classList.add('slideIn');

        // Supprime l'animation après 300ms
        setTimeout(() => panel.classList.remove('slideIn'), 300);

        // Charge les données via AJAX
        fetch('/includes/mini-messagerie.php')
            .then(response => response.text())
            .then(data => {
                content.innerHTML = data;
                scrollToBottom();
            })
            .catch(error => {
                content.innerHTML = '<div class="text-danger">Erreur de chargement.</div>';
                console.error(error);
            });

        scrollToBottom();
    }
}

function scrollToBottom() {
    const messagesList = document.getElementById('messagesList');
    if (messagesList) {
        messagesList.scrollTop = messagesList.scrollHeight;
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

// Scroll auto au chargement
document.addEventListener('DOMContentLoaded', function () {
    scrollToBottom();
});
</script>
<script>
function toggleChat() {
    const panel = document.getElementById('chatPanel');
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        loadChatPreview();
    } else {
        panel.style.display = 'none';
    }
}

function loadChatPreview() {
    fetch("includes/mini-messagerie.php")

        .then(res => res.text())
        .then(html => {
            document.getElementById("chatContent").innerHTML = html;
        });
}

// Met à jour la messagerie toutes les 30 secondes
setInterval(() => {
    const panel = document.getElementById('chatPanel');
    if (panel.style.display !== 'none') {
        loadChatPreview();
    }
}, 30000);
</script>

