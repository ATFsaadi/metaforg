<script>
    // Gestion des likes
    document
        .querySelectorAll('.like-btn')
        .forEach(btn => {
            btn.addEventListener('click', function () {
                const postId = this.getAttribute('data-post-id');
                const isLiked = this
                    .classList
                    .contains('liked');
                const likeCountEl = this
                    .closest('.post-card')
                    .querySelector('.like-count');

                fetch('includes/like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `post_id=${postId}&action=${isLiked
                        ? 'unlike'
                        : 'like'}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this
                                .classList
                                .toggle('liked');
                            likeCountEl.textContent = data.likeCount;
                        }
                    });
            });
        });

    // Masquage des commentaires ===
    document
        .querySelectorAll('.comment-toggle')
        .forEach(btn => {
            btn.addEventListener('click', function () {
                const postId = this.getAttribute('data-post-id');
                const commentSection = document.getElementById(`comments-${postId}`);
                if (commentSection) {
                    commentSection.style.display = commentSection.style.display === 'block'
                        ? 'none'
                        : 'block';
                }
            });
        });

    // Envoi de commentaires
    document
        .querySelectorAll('.comment-form')
        .forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const postId = this.getAttribute('data-post-id');
                const input = this.querySelector('.comment-input');
                const commentText = input
                    .value
                    .trim();

                if (commentText.length === 0) 
                    return;
                
                fetch('includes/comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `post_id=${postId}&comment=${encodeURIComponent(commentText)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const commentList = this.previousElementSibling;

                            // Construction HTML sécurisée (à éviter avec des variables PHP dans JS)
                            const newComment = document.createElement('div');
                            newComment
                                .classList
                                .add('comment-item');

                            newComment.innerHTML = `
                    <img src="assets/images/profil/profil_${ <?= json_encode(
                                $_SESSION['id_u'] ?? 0
                            ) ?>}" 
                         alt="Votre avatar"
                         class="comment-avatar"
                         onerror="this.src='assets/images/profil/default.png';">
                    <div class="comment-content">
                        <div class="comment-author"><?= htmlspecialchars($_SESSION['login'] ?? 'Vous') ?></div>
                        <div class="comment-text">${data.commentText}</div>
                    </div>
                `;
                            commentList.appendChild(newComment);
                            input.value = '';

                            // Mise à jour du compteur de commentaires
                            const commentCountEl = this
                                .closest('.post-card')
                                .querySelector('.post-stats div:nth-child(2)');
                            if (commentCountEl) {
                                commentCountEl.textContent = `${data.commentCount} commentaires`;
                            }
                        }
                    });
            });
        });
</script>