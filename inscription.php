<?php
require 'config.php';
require 'user.php';

$errors = [];
$user = [
    'nom' => '',
    'email' => '',
    'password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Inscription'])) {
    $user['nom'] = $_POST['userName'];
    $user['email'] = $_POST['userEmail'];
    $user['password'] = $_POST['userPass'];

    if (empty($user['nom'])) {
        $errors['nom'] = 'Le nom est requis.';
    }

    if (empty($user['email'])) {
        $errors['email'] = 'L\'email est requis.';
    } elseif (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide.';
    }

    if (empty($user['password'])) {
        $errors['password'] = 'Le mot de passe est requis.';
    } elseif (strlen($user['password']) < 8) {
        $errors['password'] = 'Le mot de passe doit avoir au moins 8 caractères.';
    }
    if (empty($errors)) {
        if (userExists($user['email'], $pdo)) {
            $errors['email'] = "Cet email est déjà utilisé.";
        } else {
            if (addUser($user, $pdo)) {
                echo "<p style='color:green;'>Inscription réussie !</p>";
                header("Location: login.php");
                exit;
            } else {
                echo "<p style='color:red;'>Erreur lors de l'inscription.</p>";
            }
        }
    }
    

}
var_dump($user);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <form action="" method="POST">
        <label for="userName">Name:</label>
        <input type="text" id="userName" name="userName" placeholder="Enter your name..."
            value="<?= htmlspecialchars($user['nom']) ?>" required>
        <?php if (isset($errors['nom'])): ?>
        <p style="color:red;"><?= $errors['nom'] ?></p>
        <?php endif; ?>
        <br>

        <label for="userEmail">Email:</label>
        <input type="email" id="userEmail" name="userEmail" placeholder="Enter your email..."
            value="<?= htmlspecialchars($user['email']) ?>" required>
        <?php if (isset($errors['email'])): ?>
        <p style="color:red;"><?= $errors['email'] ?></p>
        <?php endif; ?>
        <br>

        <label for="userPass">Password:</label>
        <input type="password" id="userPass" name="userPass" placeholder="Enter your password..." required>
        <?php if (isset($errors['password'])): ?>
        <p style="color:red;"><?= $errors['password'] ?></p>
        <?php endif; ?>
        <br>

        <input type="submit" value="Register" name="Inscription">
    </form>
</body>

</html>