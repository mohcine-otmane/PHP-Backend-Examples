<?php
require_once 'database/config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isername = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: welcome.php");
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
<html>
    <header>
        <title>Login</title>
    </head>
    <body>
        <h1>Login</h1>
        <?php if($error):?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif;?>

        <form method="post>
            <label for="username">Username:</label><br>
            <input type="text" name="username" id="username"><br><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password"><br><br>

            <button type="submit">Login</button>
        </form>
    </body>
</html>