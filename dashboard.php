<?php
session_start();
require 'config.php';
require 'user.php';
require 'functionsTransactions.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$user = detailsUser($pdo, $userId);
$solde = soldUser($pdo, $userId);
$mois = date('m');
$annee = date('Y');

$categories = [
    'revenu' => ['Salaire', 'Bourse', 'Ventes', 'Autres'],
    'depense' => ['Logement', 'Transport', 'Alimentation', 'Santé', 'Divertissement', 'Éducation', 'Autres']
    ];

$totalRevenus = totalRevenusMois($pdo, $userId, $annee, $mois);
$totalDepenses = totalDepensesMois($pdo, $userId, $annee, $mois);

$maxRevenu = revenuMaxMois($pdo, $userId, $annee, $mois);
$maxDepense = depenseMaxMois($pdo, $userId, $annee, $mois);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
</head>

<body>
    <h2>Bienvenue, <?= htmlspecialchars($user['nom']) ?> !</h2>

    <h3>💰 Solde actuel : <?= number_format($solde, 2) ?> €</h3>

    <h3>📊 Résumé du mois (<?= date('F Y') ?>)</h3>
    <p>Revenus : <?= number_format($totalRevenus, 2) ?> €</p>
    <p>Dépenses : <?= number_format($totalDepenses, 2) ?> €</p>

    <h3>🏆 Statistiques</h3>
    <p>Revenu le plus élevé : <?= number_format($maxRevenu, 2) ?> €</p>
    <p>Dépense la plus élevée : <?= number_format($maxDepense, 2) ?> €</p>

    <h3>🏆 Total des depenses par categorie</h3>
    <?php foreach ($categories as $categoryType => $categoryNames) {
            foreach ($categoryNames as $categoryName) {
        ?>
        <div>
            <span><?= $categoryName ?> : <?= $categoryType === 'revenu' ? totalIncomesByCategory($categoryName, $pdo) : totalExpensesByCategory($categoryName, $pdo)  ?></span>
        </div>
    <?php }} ?>



    <p><a href="transactions.php">Gérer mes transactions</a></p>
    <p><a href="logout.php">Se déconnecter</a></p>
</body>

</html>