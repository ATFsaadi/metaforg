<?php
ob_start();
session_start();
include "../includes/connexion.php";
include "../includes/header-PG.php";?>
        <title>Jeux - MetaForg</title>

<h2>Nos jeux en ligne</h2>
<ul class="games-grid">
  <li>
   <a href="https://www.crazygames.fr/jeu/rally-racer-dirt" target="_blank">Rally Racer Dirt<br>
  <img src="../assets/images/jeux/rallyRacer.jpg" width="200px" alt="Jouer à Rally Racer Dirt">
</a>

  </li>
  <li>
    <a href="https://www.crazygames.fr/jeu/hazmob-fps-online-shooter" target="_blank">Hazmob FPS<br>
      <img src="../assets/images/jeux/hazmob.jpeg" width="200px" alt="Jouer à Hazmob FPS Online Shooter">
    </a>
  </li>
  <li>
    <a href="https://www.crazygames.fr/jeu/battle-arena" target="_blank">Battle Arena<br>
      <img src="../assets/images/jeux/battle.avif" width="200px" alt="Jouer à Battle Arena">
    </a>
  </li>
  <li>
    <a href="https://www.crazygames.fr/jeu/poxel-io" target="_blank">Poxel.io<br>
      <img src="../assets/images/jeux/poxel.avif" width="200px" alt="Jouer à Poxel.io">
    </a>
  </li>
  <li>
    <a href="https://www.crazygames.fr/jeu/bullet-force-multiplayer" target="_blank">Bullet Force<br>
      <img src="../assets/images/jeux/force.jpg" width="200px" alt="Jouer à Bullet Force Multiplayer">
    </a>
  </li>
  <li>
    <a href="https://www.crazygames.fr/jeu/skillwarz" target="_blank">Skillwarz<br>
      <img src="../assets/images/jeux/skillwarz.avif" width="200px" alt="Jouer à Skillwarz">
    </a>
  </li>
</ul>
<?php include '../includes/footer.php'; ?>