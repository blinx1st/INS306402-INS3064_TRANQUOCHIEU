<?php
$validUser = 'admin';
$validPass = '123456';

$statusMessage = '';
$failedAttempts = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $failedAttempts = (int)($_POST['failed_attempts'] ?? 0);

    if ($username === $validUser && $password === $validPass) {
        $statusMessage = 'Login Successful';
        $failedAttempts = 0;
    } else {
        $failedAttempts++;
        $statusMessage = 'Invalid Credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
</head>
<body>
    <h1>Login Form</h1>

    <?php if ($statusMessage !== ''): ?>
        <p><strong>
            <?php echo htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8'); ?>
        </strong></p>
        <p>Failed Attempts: 
            <?php echo $failedAttempts; 
            ?></p>
    <?php endif; ?>

    <form action="login.php" method="post">
        <div>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required>
        </div>
        <br>

        <div>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required>
        </div>
        <br>

        <input type="hidden" name="failed_attempts" value="<?php echo $failedAttempts; ?>">
        <button type="submit">Login</button>
    </form>
</body>
</html>
