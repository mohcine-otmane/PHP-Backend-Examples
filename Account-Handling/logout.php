<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="script.js" type="module" defer></script>
</head>
<body>
    <div class="container">
        <div class="profile-section">
            <div class="logout-content">
                <i class="fas fa-sign-out-alt logout-icon"></i>
                <h1 class="section-title glitch" data-text="Logging Out">
                    <i class="fas fa-power-off"></i> Logging Out
                </h1>
                <p class="logout-message">You have been successfully logged out.</p>
                <div class="redirect-message">
                    <i class="fas fa-spinner fa-spin"></i> Redirecting to login page...
                </div>
            </div>
        </div>
    </div>
</body>
</html>