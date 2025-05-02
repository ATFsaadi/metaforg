<?php
include "includes/connexion.php";?>
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
                                <img
                                    src="assets/images/default_avatar.png"
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
    function toggleChat() {
        const panel = document.getElementById('chatPanel');
        const isVisible = panel.style.display !== 'none';
        panel.style.display = isVisible
            ? 'none'
            : 'block';
        if (!isVisible) {
            panel
                .classList
                .add('slideIn');
            setTimeout(() => panel.classList.remove('slideIn'), 300);
        }
    }

    document.addEventListener('click', function (event) {
        const chatPanel = document.getElementById('chatPanel');
        const chatButton = document.getElementById('chatButton');
        if (!chatPanel.contains(event.target) && !chatButton.contains(event.target)) {
            chatPanel.style.display = 'none';
        }
    });
</script>
