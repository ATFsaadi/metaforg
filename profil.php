<?php include "includes/connexion.php";?>

<?php

$requete = $bdd->query("SELECT * FROM users 
                        WHERE 
                        id_u = ".$_SESSION['id_u']);
$reponse = $requete->fetch();

if(isset($_POST['submit']))
{
    $login = $_POST['login'];
    $email = $_POST['email'];
    $mdp = sha1($_POST['mdp']);
    $mdpConfirm = sha1($_POST['mdpConfirm']);

    if (!empty($_POST['mdp'])) {
        if ($_POST['mdp'] !== $_POST['mdpConfirm']) {
            $error = "Mots de passe différents";
        } else {
            $hash = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
            $update = $bdd->prepare("UPDATE users SET login = ?, email = ?, mdp = ? WHERE id_u = ?");
            $update->execute([$login, $email, $hash, $_SESSION['user']['id']]);
        }
    } else {
        // Mise à jour sans changer le mot de passe
        $update = $bdd->prepare("UPDATE users SET login = ?, email = ? WHERE id_u = ?");
        $update->execute([$login, $email, $_SESSION['user']['id']]);
    }
    }
    ?>

<form method="post">
    <div class="form-group">
        <label for="loginInput">Login:</label>
        <input
            type="text"
            name="login"
            value="<?php echo $reponse['login']; ?>"
            class="form-control"
            id="loginInput"
            aria-describedby="emailHelp"
            placeholder="Enter email">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1">Email:</label>
        <input
            type="email"
            name="email"
            value="<?php echo $reponse['email']; ?>"
            class="form-control"
            id="exampleInputEmail1"
            aria-describedby="emailHelp"
            placeholder="Enter email">
    </div>
    <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input
            type="password"
            name="mdp"
            class="form-control"
            id="exampleInputPassword1"
            placeholder="Password">
    </div>

    <div class="form-group">
        <label for="exampleInputPassword1">Confirmer Password</label>
        <input
            type="password"
            name="mdpConfirm"
            class="form-control"
            id="exampleInputPassword1"
            placeholder="Password">
    </div>

    <button type="submit" name="submit" class="btn btn-primary">MAJ</button>
</form>

<?php include 'includes/footer.php'; ?>