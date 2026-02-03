<?php
function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$isFinalSubmit = isset($_POST['submit_final']);
$bio = trim($_POST['bio'] ?? '');
$location = trim($_POST['location'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 2 - Profile Info</title>
</head>
<body>
    <h1>Step 2: Profile Info</h1>

    <?php if ($username === '' || $password === ''): ?>
        <p><strong>Missing account info. Please start from Step 1.</strong></p>
        <p><a href="step1.php">Go to Step 1</a></p>
    <?php elseif ($isFinalSubmit): ?>
        <h2>Final Submission</h2>
        <ul>
            <li><strong>Username:</strong> <?php echo esc($username); ?></li>
            <li><strong>Password:</strong> <?php echo esc($password); ?></li>
            <li><strong>Bio:</strong> <?php echo esc($bio); ?></li>
            <li><strong>Location:</strong> <?php echo esc($location); ?></li>
        </ul>
    <?php else: ?>
        <form action="step2.php" method="post">
            <input type="hidden" name="username" value="<?php echo esc($username); ?>">
            <input type="hidden" name="password" value="<?php echo esc($password); ?>">

            <div>
                <label for="bio">Bio:</label><br>
                <textarea id="bio" name="bio" rows="4" cols="40" required></textarea>
            </div>
            <br>

            <div>
                <label for="location">Location:</label><br>
                <input type="text" id="location" name="location" required>
            </div>
            <br>

            <button type="submit" name="submit_final" value="1">Submit All</button>
        </form>
    <?php endif; ?>
</body>
</html>
