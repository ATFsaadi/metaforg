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

function input($label, $name, $type) {
    $html = "<div class='mb-3'>
    <label for='$name' class='form-label'>$label</label>
    <input type='$type' class='form-control' id='$name' aria-describedby='emailHelp'>
    <div id='$name' class='form-text'>We'll never share your email with anyone else.</div>
  </div>";
    
    echo $html;
}

function inputTextArea($label, $name) {
    $html = "<div class='mb-3'>
    <label for='$name' class='form-label'>$label</label>
    <textarea class='form-control' id='$name' rows='3'></textarea>
  </div>";
    
    echo $html;
}

function TypeFile($label, $name) {
    $html = "<div class='mb-3'>
    <label for='$name' class='form-label'>$label</label>
    <input type='file' class='form-control' id='$name'>
  </div>";
    
    echo $html;
}

/**
 * Recherche des utilisateurs par nom d'utilisateur (login)
 * @param PDO $pdo - Connexion PDO à la base de données
 * @param string $query - Terme de recherche
 * @return array - Tableau des utilisateurs trouvés
 */
function rechercherUtilisateurs($pdo, $query) {
    // Nettoyer et préparer le terme de recherche
    $searchTerm = '%' . trim($query) . '%';
    
    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("SELECT id_u, login, email FROM users WHERE login LIKE :search OR email LIKE :search");
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
    
    // Retourner les résultats
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Affiche un formulaire de recherche d'utilisateurs
 * @param string $action - L'URL de traitement du formulaire
 * @param string $method - La méthode HTTP (GET ou POST)
 */
function afficherFormulaireRecherche($action = '', $method = 'GET') {
    echo "<form action=\"$action\" method=\"$method\" class=\"d-flex\">
            <input class=\"form-control me-2\" type=\"search\" name=\"q\" placeholder=\"Rechercher un utilisateur...\" aria-label=\"Search\">
            <button class=\"btn btn-outline-success\" type=\"submit\">Rechercher</button>
          </form>";
}

/**
 * Affiche les résultats de recherche d'utilisateurs avec option d'ajout en ami
 * @param array $utilisateurs - Tableau des utilisateurs trouvés
 * @param int $user_id - ID de l'utilisateur connecté (optionnel)
 */
function afficherResultatsRecherche($utilisateurs, $user_id = null) {
    if (empty($utilisateurs)) {
        echo "<div class='alert alert-info mt-3'>Aucun utilisateur trouvé</div>";
        return;
    }
    
    echo "<div class='list-group mt-3'>";
    foreach ($utilisateurs as $user) {
        // Ne pas afficher l'utilisateur connecté dans les résultats
        if ($user_id && $user['id_u'] == $user_id) {
            continue;
        }
        
        echo "<div class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "<div>";
        echo "<h5 class='mb-1'>" . htmlspecialchars($user['login']) . "</h5>";
        echo "<small>" . htmlspecialchars($user['email']) . "</small>";
        echo "</div>";
        echo "<div>";
        
        // Bouton pour voir le profil
        echo "<a href='" . (strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '' : 'pages/') . "profile.php?id=" . $user['id_u'] . "' class='btn btn-primary btn-sm me-2'><i class='fas fa-user'></i> Voir profil</a>";
        
        // Bouton pour ajouter en ami (seulement si l'utilisateur est connecté)
        if ($user_id) {
            echo "<a href='" . (strpos($_SERVER['PHP_SELF'], '/pages/') !== false ? '' : 'pages/') . "add_friend.php?friend_id=" . $user['id_u'] . "' class='btn btn-success btn-sm'><i class='fas fa-user-plus'></i> Ajouter en ami</a>";
        }
        
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
}
?>
