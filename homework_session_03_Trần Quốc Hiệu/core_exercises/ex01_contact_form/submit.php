<?php
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

$hasMissingData = ($fullName === '' || $email === '' || $phone === '' || $message === '');

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Result</title>
</head>
<body>
    <h1>Contact Form Submission</h1>

    <?php if ($hasMissingData): ?>
        <p><strong>Missing Data</strong></p>
    <?php else: ?>
        <ul>
            <li><strong>Full Name:</strong> <?= esc($fullName) ?></li>
            <li><strong>Email:</strong> <?= esc($email) ?></li>
            <li><strong>Phone Number:</strong> <?= esc($phone) ?></li>
            <li><strong>Message:</strong> <?= esc($message) ?></li>
        </ul>
    <?php endif; ?>
</body>
</html>
