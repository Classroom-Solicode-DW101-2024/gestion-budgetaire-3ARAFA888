<?php
function soldUser($pdo, $userId) {
    $revenus = totalRevenus($pdo, $userId);
    $depenses = totalDepenses($pdo, $userId);
    return $revenus - $depenses;
}

function totalRevenus($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT SUM(montant) FROM transactions 
                           JOIN categories ON transactions.category_id = categories.id 
                           WHERE categories.type = 'revenu' AND transactions.user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() ?: 0;
}

function totalDepenses($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT SUM(montant) FROM transactions 
                           JOIN categories ON transactions.category_id = categories.id 
                           WHERE categories.type = 'depense' AND transactions.user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn() ?: 0;
}

function totalRevenusMois($pdo, $userId, $annee, $mois) {
    $stmt = $pdo->prepare("SELECT SUM(montant) FROM transactions 
                           JOIN categories ON transactions.category_id = categories.id 
                           WHERE categories.type = 'revenu' AND transactions.user_id = ? 
                           AND YEAR(date_transaction) = ? AND MONTH(date_transaction) = ?");
    $stmt->execute([$userId, $annee, $mois]);
    return $stmt->fetchColumn() ?: 0;
}

function totalDepensesMois($pdo, $userId, $annee, $mois) {
    $stmt = $pdo->prepare("SELECT SUM(montant) FROM transactions 
                           JOIN categories ON transactions.category_id = categories.id 
                           WHERE categories.type = 'depense' AND transactions.user_id = ? 
                           AND YEAR(date_transaction) = ? AND MONTH(date_transaction) = ?");
    $stmt->execute([$userId, $annee, $mois]);
    return $stmt->fetchColumn() ?: 0;
}

function revenuMaxMois($pdo, $userId, $annee, $mois) {
    $stmt = $pdo->prepare("SELECT MAX(montant) FROM transactions 
                           JOIN categories ON transactions.category_id = categories.id 
                           WHERE categories.type = 'revenu' AND transactions.user_id = ? 
                           AND YEAR(date_transaction) = ? AND MONTH(date_transaction) = ?");
    $stmt->execute([$userId, $annee, $mois]);
    return $stmt->fetchColumn() ?: 0;
}

function depenseMaxMois($pdo, $userId, $annee, $mois) {
    $stmt = $pdo->prepare("SELECT MAX(montant) FROM transactions 
                           JOIN categories ON transactions.category_id = categories.id 
                           WHERE categories.type = 'depense' AND transactions.user_id = ? 
                           AND YEAR(date_transaction) = ? AND MONTH(date_transaction) = ?");
    $stmt->execute([$userId, $annee, $mois]);
    return $stmt->fetchColumn() ?: 0;
}

function insertTransaction(PDO $pdo, int $userId, int $categoryId, float $montant, string $description, string $date): void {
    $sql = "INSERT INTO transactions (user_id, category_id, montant, description, date_transaction)
            VALUES (:user_id, :category_id, :montant, :description, :date_transaction)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':category_id' => $categoryId,
        ':montant' => $montant,
        ':description' => $description,
        ':date_transaction' => $date
    ]);
}

function getOrCreateCategory(PDO $pdo, string $nom, string $type): int {
    $sql = "SELECT id FROM categories WHERE nom = :nom AND type = :type";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':type' => $type
    ]);

    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($category) {
        return $category['id'];
    }

    $insertCategory = "INSERT INTO categories (nom, type) VALUES (:nom, :type)";
    $stmt = $pdo->prepare($insertCategory);
    $stmt->execute([
        ':nom' => $nom,
        ':type' => $type
    ]);
    return $pdo->lastInsertId();
}
?>