<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérification si l'utilisateur est connecté
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
    // ✅ Mise à jour des messages comme lus
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

// Envoi de message
if (isset($_POST['send_message']) && $contact_id) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $req_insert = $bdd->prepare("
            INSERT INTO envoyer (id_exp, id_recept, message, date_env)
            VALUES (:exp_id, :recept_id, :message, NOW())
        ");
        $req_insert->execute([
            'exp_id' => $user_id,
            'recept_id' => $contact_id,
            'message' => $message
        ]);
        header("Location: messages.php?contact=" . $contact_id);
        exit;
    }
}

// Recherche d'utilisateurs
if (isset($_POST['search_user'])) {
    $search_term = '%' . $_POST['search_term'] . '%';
    $req_search = $bdd->prepare("
        SELECT id_u, login
        FROM users
        WHERE login LIKE :search_term AND id_u != :user_id
        LIMIT 10
    ");
    $req_search->execute([
        'search_term' => $search_term,
        'user_id' => $user_id
    ]);
    $search_results = $req_search->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php
$req_unread = $bdd->prepare("
    SELECT e.id_exp, u.login, COUNT(*) as nb
    FROM envoyer e
    JOIN users u ON e.id_exp = u.id_u
    WHERE e.id_recept = :user_id AND e.lu = 0
    GROUP BY e.id_exp
");
$req_unread->execute(['user_id' => $_SESSION['id_u']]);
$unread_msgs = $req_unread->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($unread_msgs)): ?>
    <div class="alert alert-info mt-3">
        <h5>📨 Messages non lus</h5>
        <ul class="list-group">
            <?php foreach ($unread_msgs as $msg): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="messages.php?contact=<?= $msg['id_exp'] ?>">
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
                    <form method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search_term" class="form-control" placeholder="Rechercher un utilisateur..." required>
                            <button type="submit" name="search_user" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <?php if (!empty($search_results)): ?>
                            <div class="search-results mt-2">
                                <?php foreach ($search_results as $result): ?>
                                    <div class="search-result-item" style="cursor:pointer" onclick="window.location.href='messages.php?contact=<?= $result['id_u'] ?>'">
                                        <?= htmlspecialchars($result['login']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </form>

                    <?php if (!empty($conversations)): ?>
                        <?php foreach ($conversations as $conversation): 
                            $cid = $conversation['contact_id'];
                            if (!$cid || !isset($users_logins[$cid])) continue;
                        ?>
                           <div class="contact-item <?= ($contact_id == $cid) ? 'bg-light' : '' ?> p-2 rounded mb-2" style="cursor:pointer" onclick="window.location.href='messages.php?contact=<?= $cid ?>'">
    <div class="d-flex align-items-center">
        <div class="fw-bold flex-grow-1"><?= htmlspecialchars($users_logins[$cid]) ?></div>
        <div class="flex-shrink-0">
            <img src="../assets/images/Profil/profil_<?= $cid ?>"  
                 class="profile-avatar msg rounded-circle"
                 alt="Avatar de <?= htmlspecialchars($cid) ?>" 
                 width="40" height="40"
                 style="object-fit: cover; border: 2px solid orange;">
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

                <!-- Zone de messages -->
                <div class="messages-area flex-grow-1 d-flex flex-column">
                    <?php if ($contact_info): ?>
                        <div class="messages-header p-3 border-bottom d-flex align-items-center">
                            <div class="fw-bold"><?= htmlspecialchars($contact_info['login']) ?></div>
                        </div>

                        <div class="messages-list flex-grow-1 overflow-auto p-3" id="messagesList" style="height: 400px;">
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

                        <form method="POST" class="d-flex border-top p-3">
                            <input type="text" name="message" class="form-control me-2" placeholder="Tapez votre message..." required maxlength="1000">
                            <button type="submit" name="send_message" class="btn btn-primary">
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

<?php ob_end_flush(); ?>
<?php include '../includes/footer.php'; ?>
