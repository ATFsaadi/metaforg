<?php
session_start();
include "../includes/connexion.php";

if (!isset($_SESSION['connecte']) || $_SESSION['connecte'] !== true) {
    http_response_code(403);
    exit("Non autorisé.");
}

$user_id = $_SESSION['id_u'];
$contact_id = isset($_GET['contact']) ? intval($_GET['contact']) : null;

if (!$contact_id) {
    echo "<div class='p-3 text-muted'>Aucun contact sélectionné</div>";
    exit;
}

// Marquer les messages comme lus
$bdd->prepare("UPDATE envoyer SET lu = 1 WHERE id_exp = :contact_id AND id_recept = :user_id AND lu = 0")
    ->execute(['contact_id' => $contact_id, 'user_id' => $user_id]);

// Récupérer les infos du contact
$req_contact = $bdd->prepare("SELECT login FROM users WHERE id_u = :contact_id");
$req_contact->execute(['contact_id' => $contact_id]);
$contact = $req_contact->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
    echo "<div class='p-3 text-danger'>Contact introuvable</div>";
    exit;
}

// Récupérer les messages
$req_messages = $bdd->prepare("
    SELECT e.*, u1.login as exp_login
    FROM envoyer e
    JOIN users u1 ON e.id_exp = u1.id_u
    WHERE (e.id_exp = :user_id AND e.id_recept = :contact_id)
       OR (e.id_exp = :contact_id AND e.id_recept = :user_id)
    ORDER BY e.date_env ASC
");
$req_messages->execute(['user_id' => $user_id, 'contact_id' => $contact_id]);
$messages = $req_messages->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Zone des messages -->
<div class="messages-header p-3 border-bottom d-flex align-items-center">
    <div class="fw-bold"><?= htmlspecialchars($contact['login']) ?></div>
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
            Aucun message avec <?= htmlspecialchars($contact['login']) ?>
        </div>
    <?php endif; ?>
</div>

<!-- Formulaire -->
<form id="messageForm" class="d-flex border-top p-3">
    <input type="text" id="messageInput" name="message" class="form-control me-2"
           placeholder="Tapez votre message..." required maxlength="1000"
           data-contact-id="<?= $contact_id ?>">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-paper-plane"></i>
    </button>
</form>
