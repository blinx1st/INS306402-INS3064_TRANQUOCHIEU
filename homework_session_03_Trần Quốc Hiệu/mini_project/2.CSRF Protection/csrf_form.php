<?php
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify CSRF token on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (empty($token) || $token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('CSRF token validation failed');
    }

    // Process form safely
    echo "Form submitted successfully!";
}

$token = $_SESSION['csrf_token'];
?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="text" name="username" required>
    <button type="submit">Submit</button>
</form>