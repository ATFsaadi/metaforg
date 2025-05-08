<?php
session_start();
include "../includes/connexion.php";

// Activer les erreurs PDO si ce n'est pas déjà fait
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['id_u'], $_POST['ami_id'])) {
        $utilisateur_id = (int)$_SESSION['id_u'];
        $ami_id = (int)$_POST['ami_id'];

        // Ne pas s'ajouter soi-même
        if ($utilisateur_id === $ami_id) {
            echo "<p style='color: red;'>❌ Vous ne pouvez pas vous ajouter vous-même.</p>";
            exit;
        }

        try {
            // Vérifier si une relation existe déjà
            $stmt = $bdd->prepare("SELECT * FROM amis 
                WHERE (utilisateur_id = ? AND ami_id = ?) 
                   OR (utilisateur_id = ? AND ami_id = ?)");
            $stmt->execute([$utilisateur_id, $ami_id, $ami_id, $utilisateur_id]);

            if ($stmt->rowCount() > 0) {
                echo "<p style='color: orange;'>⚠️ Une demande d'ami ou relation existe déjà.</p>";
            } else {
                // Insérer la demande avec statut 'en_attente'
                $stmt = $bdd->prepare("INSERT INTO amis (utilisateur_id, ami_id, statut) VALUES (?, ?, 'en_attente')");
                $stmt->execute([$utilisateur_id, $ami_id]);
                echo "<p style='color: green;'>✅ Demande d'ami envoyée avec succès.</p>";
            }

        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Erreur PDO : " . $e->getMessage() . "</p>";
        }

    } else {
        echo "<p style='color: red;'>❌ Requête invalide ou non connecté.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Mauvaise méthode HTTP.</p>";
}
?>
