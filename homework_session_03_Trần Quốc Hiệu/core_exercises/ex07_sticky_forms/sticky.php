<?php
$name = '';
$email = '';
$password = '';
$errors = [];
$success = false;

function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password too short (minimum 8 characters).';
    }

    if (count($errors) === 0) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sticky Form</title>
</head>
<body>
    <h1>Sticky Form Example</h1>

    <?php if ($success): ?>
        <p><strong>Form submitted successfully.</strong></p>
    <?php else: ?>
        <?php if (count($errors) > 0): ?>
            <p><strong>Validation failed:</strong></p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="sticky.php" method="post">
            <div>
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name" value="<?php echo esc($name); ?>">
            </div>
            <br>

            <div>
                <label for="email">Email:</label><br>
                <input type="text" id="email" name="email" value="<?php echo esc($email); ?>">
            </div>
            <br>

            <div>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password">
            </div>
            <br>

            <button type="submit">Submit</button>
        </form>
    <?php endif; ?>
</body>
</html>
