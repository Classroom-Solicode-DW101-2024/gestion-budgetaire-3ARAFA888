<?php
// session_start();
require_once 'config.php';
require_once 'user.php';
require_once 'functionsTransactions.php'; // Ensure the file exists and is correctly referenced
// require_once 'dashboard.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

$annee = $_POST['annee'] ?? date('Y');
$mois = $_POST['mois'] ?? date('m');
$transactions = listTransactionsbyMonth($pdo, $userId, $annee, $mois);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $type = htmlspecialchars(trim($_POST['type']));
    $category = htmlspecialchars(trim($_POST['category']));
    $montant = floatval($_POST['montant']);
    $description = htmlspecialchars(trim($_POST['description']));
    $date = $_POST['date_transaction'];

    if ($type && $category && $montant > 0 && $date) {
        $categoryId = getOrCreateCategory($pdo, $category, $type);
        insertTransaction($pdo, $userId, $categoryId, $montant, $description, $date);
        $success = "Transaction ajoutée avec succès.";
    } else {
        $errors[] = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Transactions</title>
</head>

<body>
    <!-- Add transaction form -->
    <form method="POST">
        <label for="type">Type:</label>
        <select name="type" id="type" required>
            <option value="revenu">Revenu</option>
            <option value="depense">Dépense</option>
        </select>

        <label for="category">Catégorie:</label>
        <input type="text" name="category" id="category" required>

        <label for="montant">Montant:</label>
        <input type="number" name="montant" id="montant" step="0.01" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description"></textarea>

        <label for="date_transaction">Date:</label>
        <input type="date" name="date_transaction" id="date_transaction" required>

        <input type="submit" name="add" value="Ajouter">
    </form>

    <?php
    if (!empty($errors)) {
        foreach ($errors as $e) {
            echo "<p style='color:red;'>$e</p>";
        }
    }
    if ($success) {
        echo "<p style='color:green;'>$success</p>";
    }
    ?>

    <!-- Displaying transactions -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Catégorie</th>
                <th>Description</th>
                <th>Montant</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?= $t['date_transaction'] ?></td>
                <td><?= $t['type'] ?></td>
                <td><?= $t['nom'] ?></td>
                <td><?= $t['description'] ?></td>
                <td><?= number_format($t['montant'], 2) ?> MAD</td>
                <td>
                    <a href="edit_transaction.php?id=<?= $t['id'] ?>">Modifier</a>
                    <a href="delete_transaction.php?id=<?= $t['id'] ?>"
                        onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Filter by year and month -->
    <form method="POST">
        <select name="annee">
            <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
            <option value="<?= $y ?>" <?= ($y == $annee ? 'selected' : '') ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>

        <select name="mois">
            <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= ($m == $mois ? 'selected' : '') ?>><?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
            </option>
            <?php endfor; ?>
        </select>

        <input type="submit" value="Filtrer">
    </form>

</body>

</html>