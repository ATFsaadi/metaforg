<?php
ob_start();

include "../includes/connexion.php";
include "../includes/header-PG.php";

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    header("Location: ../index.php");
    exit;
}

// ID de l'utilisateur connecté
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

// Si une conversation est sélectionnée
$contact_id = isset($_GET['contact']) ? intval($_GET['contact']) : null;
$contact_info = null;

// Si on a un contact sélectionné, récupérer ses informations
if ($contact_id) {
    $req_contact = $bdd->prepare("SELECT id_u, login FROM users WHERE id_u = :contact_id");
    $req_contact->execute(['contact_id' => $contact_id]);
    $contact_info = $req_contact->fetch(PDO::FETCH_ASSOC);
    
    // Si contact existe, récupérer les messages
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

// Traitement de l'envoi de message
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
        
        // Redirection pour éviter double soumission
        header("Location: messages.php?contact=" . $contact_id);
        exit;
    }
}

// Pour chercher un nouvel utilisateur
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
    <!-- Contenu principal -->
       <div class="container mt-5 pt-4">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="messages-container">
                    <!-- Liste des contacts -->
                    <div class="contacts-list">
                        <div class="new-conversation">
                            <form method="POST" action="" class="position-relative">
                                <div class="input-group">
                                    <input type="text" name="search_term" class="form-control" placeholder="Rechercher un utilisateur..." required>
                                    <button type="submit" name="search_user" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                
                                <?php if (isset($search_results) && !empty($search_results)): ?>
                                <div class="search-results mt-2">
                                    <?php foreach ($search_results as $result): ?>
                                    <div class="search-result-item" onclick="window.location.href='messages.php?contact=<?= $result['id_u'] ?>'">
<?= htmlspecialchars($result['login']) ?>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</form>
</div>

<?php foreach ($conversations as $conversation): 
                            if (empty($conversation['contact_id'])) continue;
                            
                            $req_user = $bdd->prepare("SELECT login FROM users WHERE id_u = :contact_id");
                            $req_user->execute(['contact_id' => $conversation['contact_id']]);
                            $user = $req_user->fetch(PDO::FETCH_ASSOC);
                            
                            if (!$user) continue;
                        ?>
<div
class="contact-item <?= (isset($contact_id) && $contact_id == $conversation['contact_id']) ? 'active' : '' ?>"
onclick="window.location.href='messages.php?contact=<?= $conversation['contact_id'] ?>'">
<div class="d-flex align-items-center">
<div class="me-2">
<img
src="../assets/images/default_avatar.png"
alt="Avatar"
class="rounded-circle"
width="40"
height="40">
</div>
<div>
<div class="fw-bold"><?= htmlspecialchars($user['login']) ?></div>
</div>
</div>
</div>
<?php endforeach; ?>

<?php if (empty($conversations)): ?>
<div class="p-3 text-center text-muted">
<p>Aucune conversation</p>
<p>Recherchez un utilisateur pour commencer</p>
</div>
<?php endif; ?>
</div>

<!-- Zone de messages -->
<div class="messages-area">
<?php if (isset($contact_info) && $contact_info): ?>
<div class="messages-header">
<div class="d-flex align-items-center">
<div class="me-2">
<img
src="../assets/images/default_avatar.png"
alt="Avatar"
class="rounded-circle"
width="40"
height="40">
</div>
<div>
<div class="fw-bold"><?= htmlspecialchars($contact_info['login']) ?></div>
</div>
</div>
</div>

<div class="messages-list" id="messagesList">
<?php if (isset($messages) && !empty($messages)): 
                                    foreach ($messages as $message): ?>
<div
class="message-item <?= ($message['id_exp'] == $user_id) ? 'message-sent' : 'message-received' ?>">
<?= nl2br(htmlspecialchars($message['message'])) ?>
<div class="message-time">
<?= date('H:i', strtotime($message['date_env'])) ?>
</div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="text-center text-muted mt-3">
<p>Aucun message</p>
<p>Commencez à discuter avec
<?= htmlspecialchars($contact_info['login']) ?></p>
</div>
<?php endif; ?>
</div>

<form class="message-form" method="POST" action="">
<input
type="text"
name="message"
class="message-input"
placeholder="Tapez votre message..."
required="required">
<button type="submit" name="send_message" class="send-button">
<i class="fas fa-paper-plane"></i>
</button>
</form>
<?php else: ?>
<div class="empty-state">
<i class="fas fa-comments"></i>
<h3>Bienvenue dans votre messagerie</h3>
<p>Sélectionnez une conversation ou démarrez-en une nouvelle</p>
</div>
<?php endif; ?>
</div>
</div>
</div>
</div></div><!-- Bootstrap Bundle with Popper --><script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script><script>
// Faire défiler automatiquement vers le bas de la conversation
document.addEventListener('DOMContentLoaded', function () {
const messagesList = document.getElementById('messagesList');
if (messagesList) {
messagesList.scrollTop = messagesList.scrollHeight;
}
});</script> </body></html><?php ob_end_flush(); ?><?php include '../includes/footer.php'; ?>