<?php

$host = 'localhost';
$db = 'Accounts';
$user = 'root';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();

    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50),
        last_name VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Add new columns if they don't exist
    $columns = [
        'bio' => 'TEXT',
        'location' => 'VARCHAR(100)',
        'website' => 'VARCHAR(255)',
        'profile_picture' => 'VARCHAR(255)'
    ];

    foreach ($columns as $column => $type) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN $column $type");
        } catch (PDOException $e) {
            // Ignore error if column already exists
            if ($e->getCode() != '42S21') {
                throw $e;
            }
        }
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>