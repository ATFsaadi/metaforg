<?php
session_start();
include "../includes/connexion.php";

if (!isset($_SESSION['connecte'])) {
    http_response_code(403);
    exit;
}

$user_id = $_SESSION['id_u'];
$contact_id = isset($_POST['contact']) ? intval($_POST['contact']) : 0;
$message = trim($_POST['message']);

if (!empty($message) && $contact_id) {
    $req_insert = $bdd->prepare("
        INSERT INTO envoyer (id_exp, id_recept, message, date_env)
        VALUES (:exp_id, :recept_id, :message, NOW())
    ");
    $req_insert->execute([
        'exp_id' => $user_id,
        'recept_id' => $contact_id,
        'message' => $message
    ]);
    echo "OK";
}
?>