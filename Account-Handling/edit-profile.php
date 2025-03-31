<?php
require_once 'database/config.php';
require_once 'includes/profile-picture-handler.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$successMessage = '';
$errorMessage = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if(!$user) {
        die("That user is not in our database");
    }

    $email = $user['email'];
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];
    $bio = $user['bio'] ?? '';
    $location = $user['location'] ?? '';
    $website = $user['website'] ?? '';
    $profilePicture = $user['profile_picture'] ?? 'default.jpg';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $errorMessage = 'Invalid request';
        } else {
            $newEmail = trim($_POST['email']);
            $newFirstName = trim($_POST['first_name']);
            $newLastName = trim($_POST['last_name']);
            $newBio = trim($_POST['bio']);
            $newLocation = trim($_POST['location']);
            $newWebsite = trim($_POST['website']);

            try {
                // Handle profile picture upload
                if (isset($_FILES['profile_picture'])) {
                    $profilePicture = handleProfilePictureUpload($_FILES['profile_picture'], $profilePicture);
                }

                $updateStmt = $pdo->prepare("UPDATE users SET email = ?, first_name = ?, last_name = ?, bio = ?, location = ?, website = ?, profile_picture = ? WHERE id = ?");
                $updateStmt->execute([$newEmail, $newFirstName, $newLastName, $newBio, $newLocation, $newWebsite, $profilePicture, $user_id]);

                $successMessage = "Profile updated successfully!";
                
                // Update local variables
                $email = $newEmail;
                $firstName = $newFirstName;
                $lastName = $newLastName;
                $bio = $newBio;
                $location = $newLocation;
                $website = $newWebsite;
            } catch(Exception $e) {
                $errorMessage = "Profile not updated: " . $e->getMessage();
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
    <title>Edit Profile</title>
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
            <h2 class="section-title">
                <i class="fas fa-user-edit"></i> Edit Profile Information
            </h2>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="profile_picture">
                        <i class="fas fa-camera"></i> Profile Picture
                    </label>
                    <div class="profile-picture-upload">
                        <img src="<?php echo htmlspecialchars('uploads/profile-pictures/' . $profilePicture); ?>" 
                             alt="Current Profile Picture" class="preview-image">
                        <div class="file-input-wrapper">
                            <input type="file" name="profile_picture" id="profile_picture" 
                                   accept="image/jpeg,image/png,image/gif">
                            <label for="profile_picture" class="action-button">
                                <i class="fas fa-upload"></i> Choose File
                            </label>
                        </div>
                        <div class="upload-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Max size: 10MB. Supported formats: JPG, PNG, GIF</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" id="email" class="form-input" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-user"></i> First Name
                    </label>
                    <input type="text" name="first_name" id="first_name" class="form-input" value="<?php echo htmlspecialchars($firstName); ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-user"></i> Last Name
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-input" value="<?php echo htmlspecialchars($lastName); ?>" required>
                </div>

                <div class="form-group">
                    <label for="bio">
                        <i class="fas fa-pen"></i> Bio
                    </label>
                    <textarea name="bio" id="bio" class="form-input" rows="4" placeholder="Tell us about yourself"><?php echo htmlspecialchars($bio); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="location">
                        <i class="fas fa-map-marker-alt"></i> Location
                    </label>
                    <input type="text" name="location" id="location" class="form-input" value="<?php echo htmlspecialchars($location); ?>" placeholder="Where are you?">
                </div>

                <div class="form-group">
                    <label for="website">
                        <i class="fas fa-globe"></i> Website
                    </label>
                    <input type="url" name="website" id="website" class="form-input" value="<?php echo htmlspecialchars($website); ?>" placeholder="Your website URL">
                </div>

                <button type="submit" name="update_profile" class="action-button">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>

        <div class="danger-zone-container">
            <button type="button" class="action-button danger" id="toggleDangerZone">
                <i class="fas fa-exclamation-triangle"></i>
                Show Danger Zone
            </button>

            <div class="danger-zone" id="dangerZone" style="display: none;">
                <div class="section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Danger Zone
                </div>
                <p>Once you delete your account, there is no going back. Please be certain.</p>
                <form method="POST" action="delete-account.php" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.');">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <button type="submit" name="delete_account" class="action-button danger">
                        <i class="fas fa-trash"></i>
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggleDangerZone');
        const dangerZone = document.getElementById('dangerZone');
        
        if (toggleButton && dangerZone) {
            toggleButton.addEventListener('click', function() {
                const isHidden = dangerZone.style.display === 'none';
                dangerZone.style.display = isHidden ? 'block' : 'none';
                toggleButton.innerHTML = isHidden ? 
                    '<i class="fas fa-exclamation-triangle"></i> Hide Danger Zone' : 
                    '<i class="fas fa-exclamation-triangle"></i> Show Danger Zone';
                
                // Add animation
                if (isHidden) {
                    dangerZone.style.opacity = '0';
                    dangerZone.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        dangerZone.style.opacity = '1';
                        dangerZone.style.transform = 'translateY(0)';
                    }, 10);
                }
            });
        }
    });
    </script>

    <script src="script.js" type="module"></script>
</body>
</html> 