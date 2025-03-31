<?php
require_once 'database/config.php';

$error = '';
$success = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid request';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($firstName) || empty($lastName)) {
            $error = 'All fields are required';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address';
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters long';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = 'Username can only contain letters, numbers, and underscores';
        } else {
            $passwordErrors = validatePassword($password, $passwordRequirements);
            if (!empty($passwordErrors)) {
                $error = implode('<br>', $passwordErrors);
            } else {
                try {
                    // Check if username already exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->rowCount() > 0) {
                        $error = 'Username already exists';
                    } else {
                        // Check if email already exists
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        if ($stmt->rowCount() > 0) {
                            $error = 'Email already exists';
                        } else {
                            // Insert new user
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$username, $email, $hashedPassword, $firstName, $lastName]);
                            
                            $success = 'Account created successfully! You can now login.';
                        }
                    }
                } catch(PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="script.js" type="module" defer></script>
</head>
<body>
    <div class="container">
        <div class="profile-section">
            <h1 class="section-title glitch" data-text="Create Account">
                <i class="fas fa-user-plus"></i> Create Account
            </h1>
            
            <?php if($error): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" id="signupForm" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" name="username" id="username" class="form-input" required 
                           placeholder="Choose a username" minlength="3" pattern="[a-zA-Z0-9_]+"
                           title="Username can only contain letters, numbers, and underscores">
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" id="email" class="form-input" required 
                           placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-user"></i> First Name
                    </label>
                    <input type="text" name="first_name" id="first_name" class="form-input" required 
                           placeholder="Enter your first name">
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-user"></i> Last Name
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-input" required 
                           placeholder="Enter your last name">
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" name="password" id="password" class="form-input" required 
                               placeholder="Create a password" minlength="<?php echo $passwordRequirements['minLength']; ?>">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="password-requirements">
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

                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <div class="password-toggle">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input" required 
                               placeholder="Confirm your password">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="action-button">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-links">
                <p>Already have an account? 
                    <a href="login.php" class="neon-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
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

        document.getElementById('password').addEventListener('input', function(e) {
            validatePassword(e.target.value);
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (!validatePassword(password)) {
                e.preventDefault();
                alert('Please ensure your password meets all requirements.');
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        });
    </script>
</body>
</html>
