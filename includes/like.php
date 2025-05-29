<script>
// Gestion des likes
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        const isLiked = this.classList.contains('liked');
        const likeCountEl = this.closest('.post-card').querySelector('.like-count');
        
        fetch('includes/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}&action=${isLiked ? 'unlike' : 'like'}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.classList.toggle('liked');
                likeCountEl.textContent = data.likeCount;
            }
        });
    });
});

// Gestion des commentaires - Affichage/masquage
document.querySelectorAll('.comment-toggle').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.getAttribute('data-post-id');
        const commentSection = document.getElementById(`comments-${postId}`);
        commentSection.style.display = commentSection.style.display === 'block' ? 'none' : 'block';
    });
});

// Gestion de l'envoi de commentaires
document.querySelectorAll('.comment-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const postId = this.getAttribute('data-post-id');
        const input = this.querySelector('.comment-input');
        const commentText = input.value.trim();
        
        if (commentText) {
            fetch('includes/comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `post_id=${postId}&comment=${encodeURIComponent(commentText)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajouter le nouveau commentaire à la liste
                    const commentList = this.previousElementSibling;
                    const newComment = `
                        <div class="comment-item">
                            <img src="assets/images/profil/profil_<?= $_SESSION['id_u'] ?? 0 ?>" 
                                 alt="Votre avatar"
                                 class="comment-avatar"
                                 onerror="this.src='assets/images/profil/default.png';">
                            <div class="comment-content">
                                <div class="comment-author"><?= htmlspecialchars($_SESSION['login'] ?? 'Vous') ?></div>
                                <div class="comment-text">${data.commentText}</div>
                            </div>
                        </div>
                    `;
                    commentList.insertAdjacentHTML('beforeend', newComment);
                    input.value = '';
                    
                    // Mettre à jour le compteur de commentaires
                    const commentCountEl = this.closest('.post-card').querySelector('.post-stats div:nth-child(2)');
                    commentCountEl.textContent = `${data.commentCount} commentaires`;
                }
            });
        }
    });
});
</script>