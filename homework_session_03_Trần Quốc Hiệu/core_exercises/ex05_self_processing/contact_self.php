<?php
$fullName = '';
$email = '';
$phone = '';
$message = '';

$error = '';
$isSubmittedSuccessfully = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($fullName === '' || $email === '' || $phone === '' || $message === '') {
        $error = 'Missing Data';
    } else {
        $isSubmittedSuccessfully = true;
    }
}

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
    <title>Self-Processing Contact Form</title>
</head>
<body>
    <h1>Contact Form</h1>

    <?php if ($isSubmittedSuccessfully): ?>
        <p><strong>Thank You!</strong> Your contact information has been submitted successfully.</p>
    <?php else: ?>
        <?php if ($error !== ''): ?>
            <p><strong><?php echo esc($error); ?></strong></p>
        <?php endif; ?>

        <form action="contact_self.php" method="post">
            <div>
                <label for="full_name">Full Name:</label><br>
                <input type="text" id="full_name" name="full_name" value="<?php echo esc($fullName); ?>">
            </div>
            <br>

            <div>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" value="<?php echo esc($email); ?>">
            </div>
            <br>

            <div>
                <label for="phone">Phone Number:</label><br>
                <input type="text" id="phone" name="phone" value="<?php echo esc($phone); ?>">
            </div>
            <br>

            <div>
                <label for="message">Message:</label><br>
                <textarea id="message" name="message" rows="5" cols="40"><?php echo esc($message); ?></textarea>
            </div>
            <br>

            <button type="submit">Send</button>
        </form>
    <?php endif; ?>
</body>
</html>
