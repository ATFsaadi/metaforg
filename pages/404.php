<?php
ob_start();
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - MetaForg</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        
        .error-container {
            background-color: #1E1E1E;
            border-radius: 10px;
            padding: 40px;
            max-width: 800px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.7);
            text-align: center;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            color: #00FF00;
            text-shadow: 0 0 10px #00FF00, 0 0 20px #00FF00;
            animation: glow 1.5s ease-in-out infinite alternate;
        }
        
        .error-message {
            font-size: 24px;
            margin: 20px 0;
        }
        
        .error-description {
            margin-bottom: 30px;
            color: #B0B0B0;
        }
        
        .home-button {
            background-color: #00FF00;
            color: #121212;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        
        .home-button:hover {
            background-color: #FFFFFF;
            box-shadow: 0 0 15px #00FF00;
            transform: scale(1.05);
        }
        
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        
        @keyframes glow {
            from {
                text-shadow: 0 0 5px #00FF00, 0 0 10px #00FF00;
            }
            to {
                text-shadow: 0 0 10px #00FF00, 0 0 20px #00FF00, 0 0 30px #00FF00;
            }
        }
        
        /* Animation pour les particules */
        .particle {
            position: absolute;
            background-color: #00FF00;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            animation: float 3s infinite ease-in-out;
            opacity: 0.3;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.3;
            }
            50% {
                opacity: 0.8;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0.3;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="particles" id="particles"></div>
        <h1 class="error-code">404</h1>
        <h2 class="error-message">Page non trouvée</h2>
        <p class="error-description">La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <a href="index.php" class="home-button">Retour à l'accueil</a>
    </div>
    
    <script>
        // Création de particules pour l'animation
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles');
            const containerWidth = particlesContainer.offsetWidth;
            const containerHeight = particlesContainer.offsetHeight;
            
            // Créer 50 particules
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Position aléatoire
                const posX = Math.random() * containerWidth;
                const posY = Math.random() * containerHeight;
                
                // Taille aléatoire
                const size = Math.random() * 4 + 1;
                
                // Délai et durée d'animation aléatoires
                const animationDelay = Math.random() * 5;
                const animationDuration = Math.random() * 5 + 2;
                
                particle.style.left = posX + 'px';
                particle.style.top = posY + 'px';
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.animationDelay = animationDelay + 's';
                particle.style.animationDuration = animationDuration + 's';
                
                particlesContainer.appendChild(particle);
            }
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
