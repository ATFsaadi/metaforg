<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=forum1;charset=utf8',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Erreur BDD : ' . $e->getMessage());
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>