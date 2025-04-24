<?php
session_start();
require 'config.php';
require 'functionsTransactions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $montant = $_POST['montant'];
    $description = $_POST['description'] ?? '';
    $date = $_POST['date_transaction'];
    $categoryNom = $_POST['category'];
    $categoryType = $_POST['type'];

    $categoryId = getOrCreateCategory($pdo, $categoryNom, $categoryType);

    insertTransaction($pdo, $userId, $categoryId, $montant, $description, $date);

    echo "✅ Transaction insérée avec succès.";
}

$categories = [
'revenu' => ['Salaire', 'Bourse', 'Ventes', 'Autres'],
'depense' => ['Logement', 'Transport', 'Alimentation', 'Santé', 'Divertissement', 'Éducation', 'Autres']
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>l'ajoute d'une transiction</title>
</head>

<body>


    <form method="POST">
        <label>Type :</label>
        <select name="type" id="typeSelect" required onchange="updateCategories()">
            <option value="">-- Sélectionnez --</option>
            <option value="revenu">Revenu</option>
            <option value="depense">Dépense</option>
        </select>

        <label>Catégorie :</label>
        <select name="category" id="categorySelect" required>
            <option value="">-- Choisissez d'abord un type --</option>
        </select>

        <label>Montant :</label>
        <input type="number" name="montant" min="0" step="1" pattern="\d+" inputmode="numeric" />

        <label>Description :</label>
        <input type="text" name="description">

        <label>Date :</label>
        <input type="date" name="date_transaction" required>

        <input type="submit" value="Ajouter">
    </form>

    <script>
    let categories = {
        revenu: <?= json_encode($categories['revenu']) ?>,
        depense: <?= json_encode($categories['depense']) ?>
    };

    function updateCategories() {
        let type = document.getElementById('typeSelect').value;
        let categorySelect = document.getElementById('categorySelect');

        categorySelect.innerHTML = '';

        if (type && categories[type]) {
            categories[type].forEach(cat => {
                let option = document.createElement('option');
                option.value = cat;
                option.textContent = cat;
                categorySelect.appendChild(option);
            });
        } else {
            let option = document.createElement('option');
            option.textContent = '-- Choisissez d\'abord un type --';
            categorySelect.appendChild(option);
        }
    }
    </script>
</body>

</html>