<?php
ob_end_flush();
?>
    <footer class="mt-5">
        <div class="container">
            <div class="social-icons d-flex justify-content-center mb-3">
                <a href="https://www.facebook.com" target="_blank" class="social-icon mx-2">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://x.com" target="_blank" class="social-icon mx-2">
                    <i class="fab fa-x"></i>
                </a>
                <a href="https://www.youtube.com" target="_blank" class="social-icon mx-2">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://www.twitch.tv" target="_blank" class="social-icon mx-2">
                    <i class="fab fa-twitch"></i>
                </a>
                <a href="https://discord.com" target="_blank" class="social-icon mx-2">
                    <i class="fab fa-discord"></i>
                </a>
            </div>
            
            <p class="text-center text-muted small">© 2025 MetaForg - Tous droits réservés</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // === Effet hover sur les actions de post ===
        document.addEventListener('DOMContentLoaded', function() {
            const postActions = document.querySelectorAll('.post-action');
            postActions.forEach(action => {
                action.addEventListener('mouseenter', () => {
                    action.style.backgroundColor = 'rgba(0, 191, 255, 0.1)';
                });
                action.addEventListener('mouseleave', () => {
                    action.style.backgroundColor = 'transparent';
                });
            });
        });
    </script>

</body>
</html>


