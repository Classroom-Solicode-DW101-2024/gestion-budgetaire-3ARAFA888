<?php
session_start();  
require 'config.php';
require 'user.php';

$email = $password = "";


$emailError = $passwordError = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['userEmail'];
    $password = $_POST['userPass'];

    if (empty($email)) {
        $emailError = "L'email est requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "L'email n'est pas valide.";
    }

    if (empty($password)) {
        $passwordError = "Le mot de passe est requis.";
    }

    if (empty($emailError) && empty($passwordError)) {
        $loginSuccessful = loginUser($email, $password, $pdo);
        
        if ($loginSuccessful) {
            header("Location: dashboard.php");  
            exit();  
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>

<body>
    <h2>Connexion</h2>

    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <form action="" method="POST">
        <label for="userEmail">Email:</label>
        <input type="email" id="userEmail" name="userEmail" placeholder="Enter your email..."
            value="<?= htmlspecialchars($email) ?>" required>
        <?php if (!empty($emailError)) { echo "<p style='color: red;'>$emailError</p>"; } ?>
        <br>

        <label for="userPass">Password:</label>
        <input type="password" id="userPass" name="userPass" placeholder="Enter your password..." required>
        <?php if (!empty($passwordError)) { echo "<p style='color: red;'>$passwordError</p>"; } ?>
        <br>

        <input type="submit" value="Login">
    </form>

    <p>Pas encore inscrit? <a href="inscription.php">Créer un compte</a></p>
</body>

</html>