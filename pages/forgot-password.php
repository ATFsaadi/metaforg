<?php
$mdp = ForgotPassword(); 
$bdd->query("UPDATE users SET mdp = '$mdp' WHERE id_u = '$_GET[id]'");

?>
