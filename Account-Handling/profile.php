<?php
    require_once 'database/config';

    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }


    $user_id = $_SESSION['user_id'];

    try{
        $stmt = $pdo->prapare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if(!$user) {
            die("That user is not on our database");
        }

        $email = $user['email'];
        $firstName = $user['first_name'];
        $lastName = $user['last_name'];

        try {
            $updateStmt = $pdo->prepare("UPDATE users SET email = ?, first_name = ?. last_name = ? WHERE id = ?");
            $updateStmt->execute([$newEmail, $newFirstname, $newLastName, $newLastName, $user_id]);

            $email = $newEmail;
            $firstName = newFirstName;
            $lastName = $newLastName;

            $successMessage = "Profile updated";
        } catch(PDOException $e) {
            $errorMessage
        }
    }