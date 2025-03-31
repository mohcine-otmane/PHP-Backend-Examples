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
        // Check if created_at column exists, if not add it
        $checkColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'created_at'");
        if ($checkColumn->rowCount() == 0) {
            $pdo->exec("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if(!$user) {
            die("That user is not in our database");
        }

        $email = $user['email'];
        $firstName = $user['first_name'];
        $lastName = $user['last_name'];
        $username = $user['username'];
        $profilePicture = $user['profile_picture'] ?? 'default.jpg';
        $bio = $user['bio'] ?? '';
        $location = $user['location'] ?? '';
        $website = $user['website'] ?? '';
        
        // Handle created_at with a fallback
        $createdAt = isset($user['created_at']) ? new DateTime($user['created_at']) : new DateTime('now');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_profile'])) {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    $errorMessage = 'Invalid request';
                } else {
                    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                    $firstName = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
                    $lastName = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
                    $bio = filter_var($_POST['bio'], FILTER_SANITIZE_STRING);
                    $location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
                    $website = filter_var($_POST['website'], FILTER_SANITIZE_URL);

                    // Validate email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errorMessage = "Invalid email format";
                    } else {
                        try {
                            // Check if email is already taken by another user
                            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                            $stmt->execute([$email, $user_id]);
                            if ($stmt->rowCount() > 0) {
                                $errorMessage = "Email already taken";
                            } else {
                                // Update user profile
                                $updateStmt = $pdo->prepare("UPDATE users SET 
                                    email = ?, 
                                    first_name = ?, 
                                    last_name = ?,
                                    bio = ?,
                                    location = ?,
                                    website = ?
                                    WHERE id = ?");
                                
                                $updateStmt->execute([
                                    $email,
                                    $firstName,
                                    $lastName,
                                    $bio,
                                    $location,
                                    $website,
                                    $user_id
                                ]);
                                
                                $successMessage = "Profile updated successfully!";
                            }
        } catch(PDOException $e) {
            $errorMessage = "Profile not updated: " . $e->getMessage();
                        }
                    }
                }
            } elseif (isset($_POST['change_password'])) {
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
            } elseif (isset($_POST['delete_account'])) {
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    $errorMessage = 'Invalid request';
                } else {
                    $confirmPassword = $_POST['confirm_password'];
                    if (!password_verify($confirmPassword, $user['password'])) {
                        $errorMessage = "Password is incorrect";
                    } else {
                        try {
                            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                            $deleteStmt->execute([$user_id]);
                            session_destroy();
                            header("Location: login.php");
                            exit();
                        } catch(PDOException $e) {
                            $errorMessage = "Account not deleted: " . $e->getMessage();
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
        <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="script.js" type="module" defer></script>
    </head>

    <body>
    <nav class="nav">
        <ul>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="edit-profile.php"><i class="fas fa-edit"></i> Edit Profile</a></li>
            <li><a href="change-password.php"><i class="fas fa-key"></i> Change Password</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars('uploads/profile-pictures/' . ($user['profile_picture'] ?? 'default.jpg')); ?>" alt="Profile Picture" class="profile-picture">
            <div class="profile-info">
                <h1 class="profile-name glitch" data-text="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>">
                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </h1>
                <div class="profile-username">
                    <i class="fas fa-at"></i>
                    <?php echo htmlspecialchars($user['username']); ?>
                </div>
                <?php if ($user['bio']): ?>
                    <div class="profile-bio">
                        <i class="fas fa-quote-left"></i>
                        <?php echo htmlspecialchars($user['bio']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($user['location']): ?>
                    <div class="profile-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($user['location']); ?>
                    </div>
                <?php endif; ?>
                <?php if ($user['website']): ?>
                    <div class="profile-website">
                        <i class="fas fa-globe"></i>
                        <a href="<?php echo htmlspecialchars($user['website']); ?>" target="_blank">
                            <?php echo htmlspecialchars($user['website']); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo $createdAt->format('M Y'); ?>
                        </div>
                        <div class="stat-label">Member Since</div>
                    </div>
                </div>
            </div>
        </div>

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
        <!-- <div class="profile-actions">
            <a href="edit-profile.php" class="action-button">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            <a href="change-password.php" class="action-button">
                <i class="fas fa-key"></i> Change Password
            </a>
            <a href="logout.php" class="action-button danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div> -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-user-shield"></i> Account Security
            </h2>
            <div class="security-info">
                <div class="security-item">
                    <i class="fas fa-shield-alt"></i>
                    <div class="security-details">
                        <h3>Password</h3>
                        <p>Last changed: <?php echo $createdAt->format('M d, Y'); ?></p>
                    </div>
                </div>
                <div class="security-item">
                    <i class="fas fa-envelope-shield"></i>
                    <div class="security-details">
                        <h3>Email</h3>
                        <p><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
    </body>
</html>