<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['id_u'];

// Récupération des conversations
$req_conversations = $bdd->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN id_exp = :user_id THEN id_recept
            WHEN id_recept = :user_id THEN id_exp
        END as contact_id
    FROM envoyer
    WHERE id_exp = :user_id OR id_recept = :user_id
    ORDER BY contact_id
");
$req_conversations->execute(['user_id' => $user_id]);
$conversations = $req_conversations->fetchAll(PDO::FETCH_ASSOC);

// Précharger les logins des contacts
$users_logins = [];
$contact_ids = array_column($conversations, 'contact_id');

if (!empty($contact_ids)) {
    $in = implode(',', array_fill(0, count($contact_ids), '?'));
    $req_users = $bdd->prepare("SELECT id_u, login FROM users WHERE id_u IN ($in)");
    $req_users->execute($contact_ids);
    foreach ($req_users->fetchAll(PDO::FETCH_ASSOC) as $u) {
        $users_logins[$u['id_u']] = $u['login'];
    }
}

// ID du contact sélectionné
$contact_id = isset($_GET['contact']) ? intval($_GET['contact']) : null;
$contact_info = null;

// Si un contact est sélectionné
if ($contact_id) {
    // Mise à jour des messages comme lus
    $bdd->prepare("UPDATE envoyer SET lu = 1 WHERE id_exp = :contact_id AND id_recept = :user_id AND lu = 0")
        ->execute([
            'contact_id' => $contact_id,
            'user_id' => $user_id
        ]);

    // Récupération des infos du contact
    $req_contact = $bdd->prepare("SELECT id_u, login FROM users WHERE id_u = :contact_id");
    $req_contact->execute(['contact_id' => $contact_id]);
    $contact_info = $req_contact->fetch(PDO::FETCH_ASSOC);

    // Récupération des messages
    if ($contact_info) {
        $req_messages = $bdd->prepare("
            SELECT e.*, u1.login as exp_login, u2.login as recept_login 
            FROM envoyer e
            JOIN users u1 ON e.id_exp = u1.id_u
            JOIN users u2 ON e.id_recept = u2.id_u
            WHERE (e.id_exp = :user_id AND e.id_recept = :contact_id)
               OR (e.id_exp = :contact_id AND e.id_recept = :user_id)
            ORDER BY e.date_env ASC
        ");
        $req_messages->execute([
            'user_id' => $user_id,
            'contact_id' => $contact_id
        ]);
        $messages = $req_messages->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Envoi de message via AJAX - plus besoin de traitement PHP ici
?>

<h2 class="section-title text-center">
    Messages non lus
</h2>

<?php 
// Récupération des messages non lus
$req_unread = $bdd->prepare("
    SELECT e.id_exp, u.login, COUNT(*) as nb
    FROM envoyer e
    JOIN users u ON e.id_exp = u.id_u
    WHERE e.id_recept = :user_id AND e.lu = 0
    GROUP BY e.id_exp, u.login
    ORDER BY MAX(e.date_env) DESC
");
$req_unread->execute(['user_id' => $_SESSION['id_u']]);
$unread_msgs = [];
foreach ($req_unread->fetchAll(PDO::FETCH_ASSOC) as $msg) {
    $unread_msgs[$msg['id_exp']] = $msg;
}

if (!empty($unread_msgs)): ?>
    <div id="notifications-page">
        <ul class="notifications-list">
            <?php foreach ($unread_msgs as $msg): ?>
                <li class="notification">
                    <a href="messages.php?contact=<?= $msg['id_exp'] ?>" class="notification-link">
                        <strong><?= htmlspecialchars($msg['login']) ?></strong> vous a envoyé <?= $msg['nb'] ?> message(s).
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="container mt-5 pt-4" id="reche-messag">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="messages-container d-flex">
                <!-- Liste des contacts -->
                <div class="contacts-list p-3 border-end" style="width: 300px;">
                    <!-- Formulaire de recherche AJAX -->
                    <form id="searchForm" class="mb-3">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un utilisateur..." required>
                            <button type="submit" class="btn btn-outline-primary bg-transparent">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <div id="searchResultsContainer"></div>

                    <!-- Conteneur scrollable pour la liste des contacts -->
                    <div class="contacts-list-scroll">
                        <?php if (!empty($conversations)): ?>
                            <?php foreach ($conversations as $conversation): 
                                $cid = $conversation['contact_id'];
                                if (!$cid || !isset($users_logins[$cid])) continue;
                                
                                $unread_count = isset($unread_msgs[$cid]) ? $unread_msgs[$cid]['nb'] : 0;
                            ?>
                                <div class="contact-item <?= ($contact_id == $cid) ? 'bg-light' : '' ?> p-2 rounded mb-2 contact-selector" data-contact-id="<?= $cid ?>" style="cursor:pointer">

                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold flex-grow-1"><?= htmlspecialchars($users_logins[$cid]) ?></div>
                                        <?php if ($unread_count > 0): ?>
                                            <span class="badge bg-danger rounded-pill ms-2"><?= $unread_count ?></span>
                                        <?php endif; ?>
                                        <div class="flex-shrink-0">
                                            <img src="../assets/images/Profil/profil_<?= $cid ?>"  
                                                 onerror="this.onerror=null; this.src='../assets/images/Profil/default.png';"
                                                 class="profile-avatar msg rounded-circle"
                                                 alt="Avatar de <?= htmlspecialchars($cid) ?>" 
                                                 width="40" height="40"
                                                 style="object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted mt-3">
                                <p>Aucune conversation</p>
                                <p>Recherchez un utilisateur pour commencer</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Zone de messages -->
                <div class="messages-area flex-grow-1 d-flex flex-column" id="messageContent">

                    <?php if ($contact_info): ?>
                        <div class="messages-header p-3 border-bottom d-flex align-items-center">
                            <div class="fw-bold"><?= htmlspecialchars($contact_info['login']) ?></div>
                        </div>

                        <div class="messages-list flex-grow-1 p-3" id="messagesList">
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $message): ?>
                                    <div class="message-item mb-2 <?= ($message['id_exp'] == $user_id) ? 'text-end' : 'text-start' ?>">
                                        <div class="d-inline-block p-2 rounded <?= ($message['id_exp'] == $user_id) ? 'bg-primary text-white' : 'bg-light' ?>">
                                            <?= nl2br(htmlspecialchars($message['message'])) ?>
                                            <div class="small text-muted mt-1"><?= date('H:i', strtotime($message['date_env'])) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted mt-3">
                                    <p>Aucun message</p>
                                    <p>Commencez à discuter avec <?= htmlspecialchars($contact_info['login']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Formulaire d'envoi AJAX -->
                        <form id="messageForm" class="d-flex border-top p-3">
                            <input type="text" id="messageInput" name="message" class="form-control me-2" placeholder="Tapez votre message..." required maxlength="1000">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="d-flex justify-content-center align-items-center flex-grow-1 text-center p-5">
                            <div>
                                <i class="fas fa-comments fa-3x mb-3 text-muted"></i>
                                <h3 class="mb-2">Bienvenue dans votre messagerie</h3>
                                <p>Sélectionnez une conversation ou démarrez-en une nouvelle</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion de la recherche AJAX
// Remplacer le script existant par ceci :
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const term = document.getElementById('searchInput').value.trim();
    
    if (term) {
        fetch(`recherche.php?q=${encodeURIComponent(term)}&ajax=1`)
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau');
                return response.json();
            })
            .then(results => {
                const container = document.getElementById('searchResultsContainer');
                container.innerHTML = '';
                
                if (results.length > 0) {
                    const resultsList = document.createElement('div');
                    resultsList.className = 'messages-list flex-grow-1 p-3';
                    resultsList.innerHTML = '<h5>Résultats:</h5>';
                    
                    results.forEach(user => {
                        const item = document.createElement('div');
                        item.className = 'search-result-item p-2 ';
                        item.style.cursor = 'pointer';
                       item.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <img src="../assets/images/Profil/profil_${user.id_u}"
                                            onerror="this.src='../assets/images/Profil/default.png'"
                                            class="rounded-circle me-2"
                                            width="40" height="40">
                                        <span class="fw-bold">${user.login}</span>
                                    </div>
                                `;

                        item.addEventListener('click', () => {
                            window.location.href = `messages.php?contact=${user.id_u}`;
                        });
                        resultsList.appendChild(item);
                    });
                    
                    container.appendChild(resultsList);
                } else {
                    container.innerHTML = '<div class="text-muted p-2">Aucun résultat trouvé</div>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('searchResultsContainer').innerHTML = 
                    '<div class="text-danger p-2">Erreur lors de la recherche</div>';
            });
    }
});

// Gestion de l'envoi de message AJAX
document.getElementById('messageForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = document.getElementById('messageInput').value.trim();
    const contactId = <?= $contact_id ?: 'null' ?>;
    
    if (message && contactId) {
        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `message=${encodeURIComponent(message)}&contact=${contactId}`
        })
        .then(response => response.text())
        .then(() => {
            // Ajouter le nouveau message à la liste
            const messagesList = document.getElementById('messagesList');
            const newMessage = document.createElement('div');
            newMessage.className = 'message-item mb-2 text-end';
            newMessage.innerHTML = `
                <div class="d-inline-block p-2 rounded bg-primary text-white">
                    ${message}
                    <div class="small text-muted mt-1">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                </div>
            `;
            messagesList.appendChild(newMessage);
            document.getElementById('messageInput').value = '';
            messagesList.scrollTop = messagesList.scrollHeight;
        });
    }
});

document.querySelectorAll('.contact-selector').forEach(item => {
    item.addEventListener('click', function() {
        const contactId = this.getAttribute('data-contact-id');
        if (!contactId) return;

        fetch(`messages_partial.php?contact=${contactId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('messageContent').innerHTML = html;

                // Re-bind le submit AJAX du formulaire
                const form = document.getElementById('messageForm');
                form?.addEventListener('submit', handleMessageSubmit);
            })
            .catch(err => console.error('Erreur chargement messages:', err));
    });
});

function handleMessageSubmit(e) {
    e.preventDefault();
    const message = document.getElementById('messageInput').value.trim();
    const contactId = document.getElementById('messageInput').dataset.contactId;

    if (message && contactId) {
        fetch('send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `message=${encodeURIComponent(message)}&contact=${contactId}`
        })
        .then(response => response.text())
        .then(() => {
            const messagesList = document.getElementById('messagesList');
            const newMessage = document.createElement('div');
            newMessage.className = 'message-item mb-2 text-end';
            newMessage.innerHTML = `
                <div class="d-inline-block p-2 rounded bg-primary text-white">
                    ${message}
                    <div class="small text-muted mt-1">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                </div>
            `;
            messagesList.appendChild(newMessage);
            document.getElementById('messageInput').value = '';
            messagesList.scrollTop = messagesList.scrollHeight;
        });
    }
}
</script>


<?php ob_end_flush(); ?>
<?php include '../includes/footer.php'; ?>