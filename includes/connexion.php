<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    //PHP_SESSION_NONE signifie :
    //les sessions sont activées, mais aucune n’a encore été démarrée.
}
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "function.php";
try {
    $bdd = connexion('localhost', 'forum1', 'root', '');
} catch (PDOException $e) {
    die('Erreur BDD : ' . $e->getMessage());
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>