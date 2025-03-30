<?php

$host = 'localhost';
$db = 'Accounts';
$user = 'root';
$password = '';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();
} catch (PDOEXception $e) {
    die("Connection failed: ". $e->getMessage());
}
?>