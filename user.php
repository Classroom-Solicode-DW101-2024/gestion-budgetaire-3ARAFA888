<?php
function userExists($email, $connection) {
    $stmt = $connection->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->fetch() !== false;
}


function addUser($user, $connection) {
    $sql = "INSERT INTO users (nom, email, password) VALUES (:nom, :email, :password)";
    $stmt = $connection->prepare($sql);

    $stmt->bindValue(':nom', htmlspecialchars($user['nom']));
    $stmt->bindValue(':email', htmlspecialchars($user['email']));
    $stmt->bindValue(':password', password_hash($user['password'], PASSWORD_DEFAULT));
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}





function loginUser($email, $password, $connection) {
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        return true;
    } else {
        return false;
    }
}

function detailsUser($connection, $userId) {
    $stmt = $connection->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>