<?php
// upload_avatar.php

declare(strict_types=1);

$maxSize = 2 * 1024 * 1024; // 2MB
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
];

$uploadDir = __DIR__ . '/uploads/avatars/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['avatar'])) {
    http_response_code(400);
    exit('No file uploaded.');
}

$file = $_FILES['avatar'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit('Upload failed.');
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    exit('File exceeds 2MB limit.');
}

if (!is_uploaded_file($file['tmp_name'])) {
    http_response_code(400);
    exit('Invalid upload source.');
}

// Validate MIME type from actual file content, not filename
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!isset($allowedMimeTypes[$mimeType])) {
    http_response_code(400);
    exit('Only JPG and PNG files are allowed.');
}

$extension = $allowedMimeTypes[$mimeType];

// Generate a unique safe filename to prevent overwrite
$newFileName = bin2hex(random_bytes(16)) . '.' . $extension;
$destination = $uploadDir . $newFileName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    exit('Failed to save file.');
}

// Optional: set safe permissions
chmod($destination, 0644);

echo 'Upload successful: ' . htmlspecialchars($newFileName, ENT_QUOTES, 'UTF-8');
?>
<!-- upload_form.php -->
<form action="upload_avatar.php" method="post" enctype="multipart/form-data">
    <label for="avatar">Upload avatar:</label>
    <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
    <button type="submit">Upload</button>
</form>