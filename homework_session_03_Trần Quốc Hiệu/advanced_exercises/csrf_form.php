<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $postToken = $_POST['csrf_token'] ?? '';

    if ($sessionToken === '' || $postToken === '' || !hash_equals($sessionToken, $postToken)) {
        http_response_code(403);
        exit('403 Forbidden');
    }

    echo "Gửi form thành công";
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>CSRF Form</title>
</head>

<body>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="text" name="username" placeholder="Nhập tên">
        <button type="submit">Submit</button>
    </form>
</body>

</html>