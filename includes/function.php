<?php
function connexion($host, $dbname, $user, $password) {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    return $pdo;
}
function ForgotPassword() {
    $chaine = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $mdp = "";
    $mdp .= $chaine[rand(0,25)];
    $mdp .= $chaine[rand(0,25)];
    $mdp .= $chaine[rand(26,51)];
    $mdp .= $chaine[rand(52,61)];
    $mdp .= $chaine[rand(62,91)];
    $mdp .= $chaine[rand(92,121)];
    return $mdp;
}
?>