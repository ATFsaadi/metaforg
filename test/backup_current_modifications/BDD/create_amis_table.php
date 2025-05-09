<?php
// Inclusion du fichier de connexion
include_once "../includes/connexion.php";

try {
    // Requête SQL pour créer la table amis
    $sql = "CREATE TABLE IF NOT EXISTS amis (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utilisateur_id INT NOT NULL,
        ami_id INT NOT NULL,
        statut ENUM('en_attente', 'accepte', 'refuse') DEFAULT 'en_attente',
        date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        
        CONSTRAINT fk_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES users(id_u),
        CONSTRAINT fk_ami FOREIGN KEY (ami_id) REFERENCES users(id_u),
        
        UNIQUE KEY unique_ami_pair (utilisateur_id, ami_id)
    )";
    
    // Exécution de la requête
    $bdd->exec($sql);
    
    echo "La table 'amis' a été créée avec succès!";
} catch(PDOException $e) {
    echo "Erreur lors de la création de la table: " . $e->getMessage();
}
?>
