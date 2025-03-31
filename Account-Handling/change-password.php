<?php
require_once 'database/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$successMessage = '';
$errorMessage = '';

// Password strength requirements
$passwordRequirements = [
    'minLength' => 8,
    'requireUppercase' => true,
    'requireLowercase' => true,
    'requireNumber' => true,
    'requireSpecialChar' => true
];

function validatePassword($password, $requirements) {
    $errors = [];
    if (strlen($password) < $requirements['minLength']) {
        $errors[] = "Password must be at least {$requirements['minLength']} characters long";
    }
    if ($requirements['requireUppercase'] && !preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if ($requirements['requireLowercase'] && !preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if ($requirements['requireNumber'] && !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if ($requirements['requireSpecialChar'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    return $errors;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if(!$user) {
        die("That user is not in our database");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $errorMessage = 'Invalid request';
        } else {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            if (!password_verify($currentPassword, $user['password'])) {
                $errorMessage = "Current password is incorrect";
            } elseif ($newPassword !== $confirmPassword) {
                $errorMessage = "New passwords do not match";
            } else {
                $passwordErrors = validatePassword($newPassword, $passwordRequirements);
                if (!empty($passwordErrors)) {
                    $errorMessage = implode('<br>', $passwordErrors);
                } else {
                    try {
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $updateStmt->execute([$hashedPassword, $user_id]);
                        $successMessage = "Password updated successfully!";
                    } catch(PDOException $e) {
                        $errorMessage = "Password not updated: " . $e->getMessage();
                    }
                }
            }
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="script.js" type="module" defer></script>
</head>
<body>
    <nav class="nav">
        <ul>
            <li><a href="profile.php"><i class="fas fa-arrow-left"></i> Back to Profile</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <?php if($successMessage): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if($errorMessage): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <div class="profile-section">
            <h2 class="section-title glitch" data-text="Change Password">
                <i class="fas fa-key"></i> Change Password
            </h2>
            <form method="post" action="" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="current_password">
                        <i class="fas fa-lock"></i> Current Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" name="current_password" id="current_password" class="form-input" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">
                        <i class="fas fa-key"></i> New Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" name="new_password" id="new_password" class="form-input" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-check-double"></i> Confirm New Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="password-requirements">
                    <h3><i class="fas fa-shield-alt"></i> Password Requirements</h3>
                    <ul>
                        <li id="length" class="requirement-item">
                            <i class="fas fa-check-circle"></i>
                            At least <?php echo $passwordRequirements['minLength']; ?> characters
                        </li>
                        <li id="uppercase" class="requirement-item">
                            <i class="fas fa-check-circle"></i>
                            One uppercase letter
                        </li>
                        <li id="lowercase" class="requirement-item">
                            <i class="fas fa-check-circle"></i>
                            One lowercase letter
                        </li>
                        <li id="number" class="requirement-item">
                            <i class="fas fa-check-circle"></i>
                            One number
                        </li>
                        <li id="special" class="requirement-item">
                            <i class="fas fa-check-circle"></i>
                            One special character
                        </li>
                    </ul>
                </div>

                <button type="submit" name="change_password" class="action-button" id="submitBtn">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.toggle-password');
            const icon = button.querySelector('i');
            
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        function validatePassword(password) {
            const requirements = {
                length: password.length >= <?php echo $passwordRequirements['minLength']; ?>,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            for (const [key, value] of Object.entries(requirements)) {
                const element = document.getElementById(key);
                const icon = element.querySelector('i');
                element.classList.toggle('valid', value);
                element.classList.toggle('invalid', !value);
                icon.classList.toggle('fa-check-circle', value);
                icon.classList.toggle('fa-times-circle', !value);
            }

            return Object.values(requirements).every(Boolean);
        }

        document.getElementById('new_password').addEventListener('input', function(e) {
            validatePassword(e.target.value);
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (!validatePassword(newPassword)) {
                e.preventDefault();
                alert('Please ensure your password meets all requirements.');
                return;
            }

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match.');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Password...';
        });
    </script>
</body>
</html> 