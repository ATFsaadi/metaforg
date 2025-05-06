<?php
if (!function_exists('connexion')) {
    function connexion($host, $dbname, $user, $password) {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        return $pdo;
    }
}

if (!function_exists('ForgotPassword')) {
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
}

if (!function_exists('input')) {
    function input($label, $name, $type) {
        $html = "<div class='mb-3'>
        <label for='$name' class='form-label'>$label</label>
        <input type='$type' class='form-control' id='$name' aria-describedby='emailHelp'>
        <div id='$name' class='form-text'>We'll never share your email with anyone else.</div>
      </div>";
        
        echo $html;
    }
}

if (!function_exists('inputTextArea')) {
    function inputTextArea($label, $name) {
        $html = "<div class='mb-3'>
        <label for='$name' class='form-label'>$label</label>
        <textarea class='form-control' id='$name' rows='3'></textarea>
      </div>";
        
        echo $html;
    }
}

if (!function_exists('TypeFile')) {
    function TypeFile($label, $name) {
        $html = "<div class='mb-3'>
        <label for='$name' class='form-label'>$label</label>
        <input type='file' class='form-control' id='$name'>
      </div>";
        
        echo $html;
    }
}

if (!function_exists('rechercherUtilisateurs')) {
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
}

if (!function_exists('afficherFormulaireRecherche')) {
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
}

if (!function_exists('afficherResultatsRecherche')) {
    /**
     * Affiche les résultats de recherche d'utilisateurs avec option d'ajout en ami
     * @param array $utilisateurs - Tableau des utilisateurs trouvés
     * @param int $user_id - ID de l'utilisateur connecté (optionnel)
     */
    function afficherResultatsRecherche($utilisateurs, $user_id = null) {
        if (empty($utilisateurs)) {
            echo "<div class='alert alert-info'>Aucun résultat trouvé.</div>";
            return;
        }
        
        echo "<div class='list-group mt-3'>";
        foreach ($utilisateurs as $user) {
            $idUtilisateur = htmlspecialchars($user['id_u']);
            $loginUtilisateur = htmlspecialchars($user['login']);
            $canAddFriend = isset($user_id) && $user_id != $idUtilisateur;
            $avatar = getRandomAvatar($idUtilisateur);
            
            echo "<div class='list-group-item list-group-item-action d-flex justify-content-between align-items-center'>";
            echo "<div>";
            echo "<a href='profil.php?id={$idUtilisateur}' class='text-decoration-none'>";
            echo "<img src='{$avatar}' alt='Avatar' class='rounded-circle me-2' width='30' height='30'>";
            echo "$loginUtilisateur";
            echo "</a>";
            echo "</div>";
            
            if ($canAddFriend) {
                echo "<div>";
                echo "<a href='amis.php?add={$idUtilisateur}' class='btn btn-sm btn-outline-success'><i class='fas fa-user-plus'></i> Ajouter</a>";
                echo "</div>";
            }
            
            echo "</div>";
        }
        echo "</div>";
    }
}

if (!function_exists('getRandomAvatar')) {
    /**
     * Retourne un avatar aléatoire pour un utilisateur donné
     * @param int $user_id - ID de l'utilisateur
     * @return string - Chemin de l'avatar
     */
    function getRandomAvatar($user_id = null) {
        // Déterminer si nous sommes dans un sous-répertoire ou à la racine
        $isRoot = !strpos($_SERVER['PHP_SELF'], '/pages/');
        $prefix = $isRoot ? '' : '../';
        
        // Chemin du dossier d'avatars
        $avatarDir = $prefix . 'avatar/';
        
        // Si un ID utilisateur est fourni, on utilise cet ID pour générer un avatar cohérent
        if ($user_id !== null) {
            // On utilise l'ID utilisateur pour déterminer quel avatar utiliser
            // Modulo pour s'assurer que l'index est dans les limites des avatars disponibles
            $avatarIndex = ($user_id % 7) + 1; // Il y a 7 avatars (1-6, 8)
            if ($avatarIndex == 7) $avatarIndex = 8; // Gestion du cas spécial (pas d'avatar_7.png)
            
            $avatarPath = $avatarDir . 'avatar_' . $avatarIndex . '.png';
            
            // Vérifier si le fichier existe
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/Metaforge/metaforge2/' . ltrim($avatarPath, '/'))) {
                return $avatarPath;
            }
        }
        
        // Si l'ID utilisateur n'est pas fourni ou si l'avatar n'existe pas, on en choisit un au hasard
        $avatarFiles = ['avatar_1.png', 'avatar_2.png', 'avatar_3.png', 'avatar_4.png', 'avatar_5.png', 'avatar_6.png', 'avatar_8.png'];
        $randomIndex = array_rand($avatarFiles);
        
        return $avatarDir . $avatarFiles[$randomIndex];
    }
}
?>