<?php
function handleProfilePictureUpload($file, $currentPicture = 'default.jpg') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return $currentPicture;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 10 * 1024 * 1024; // 10MB - Increased from 5MB for better quality images
    // Note: You may also need to update your PHP configuration (php.ini):
    // upload_max_filesize = 10M
    // post_max_size = 10M
    // max_execution_time = 300

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and GIF files are allowed.');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File is too large. Maximum size is 10MB.');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('profile_') . '.' . $extension;
    $uploadPath = 'uploads/profile-pictures/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to upload file.');
    }

    // Delete old profile picture if it exists and is not the default
    if ($currentPicture !== 'default.jpg' && file_exists('uploads/profile-pictures/' . $currentPicture)) {
        unlink('uploads/profile-pictures/' . $currentPicture);
    }

    return $filename;
}
?> 