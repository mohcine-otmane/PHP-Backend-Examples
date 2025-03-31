<?php
require_once 'database/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: profile.php");
                exit();
            } else {
                $error = 'Make sure your username or password are correct';
            }

        } catch(PDOException $e) {
            $error = "Database error:" . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="script.js" type="module" defer></script>
</head>
<body>
    <div class="container">
        <div class="profile-section">
            <h1 class="section-title glitch" data-text="Welcome Back">
                <i class="fas fa-user-circle"></i> Welcome Back
            </h1>
            
            <?php if($error): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="auth-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" name="username" id="username" class="form-input" required 
                           placeholder="Enter your username">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" name="password" id="password" class="form-input" required 
                           placeholder="Enter your password">
                </div>

                <button type="submit" class="action-button">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? 
                    <a href="signup.php" class="neon-link">
                        <i class="fas fa-user-plus"></i> Sign up
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>