<?php
$password = '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    // Check password requirements
    $hasUppercase = preg_match('/[A-Z]/', $password);
    $hasLowercase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSymbol = preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\/\\|`~]/', $password);
    $hasMinLength = strlen($password) >= 8;
    
    // Collect feedback
    if (empty($password)) {
        $errors[] = "Password is required.";
    } else {
        if (!$hasMinLength) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if (!$hasUppercase) {
            $errors[] = "Password must contain at least 1 uppercase letter (A-Z).";
        }
        if (!$hasLowercase) {
            $errors[] = "Password must contain at least 1 lowercase letter (a-z).";
        }
        if (!$hasNumber) {
            $errors[] = "Password must contain at least 1 number (0-9).";
        }
        if (!$hasSymbol) {
            $errors[] = "Password must contain at least 1 symbol (!@#$%^&* etc).";
        }
    }
    
    if (empty($errors)) {
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regex Password Validation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .error ul {
            margin: 0;
            padding-left: 20px;
        }
        .error li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Validator</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Password Invalid:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="success">
                <strong>✓ Password is valid and meets all requirements!</strong>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="password">Enter Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Validate Password</button>
        </form>
        
        <div style="margin-top: 20px; padding: 15px; background-color: #e7f3ff; border-radius: 4px; font-size: 14px;">
            <strong>Requirements:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Minimum 8 characters</li>
                <li>At least 1 uppercase letter (A-Z)</li>
                <li>At least 1 lowercase letter (a-z)</li>
                <li>At least 1 number (0-9)</li>
                <li>At least 1 symbol (!@#$%^&* etc)</li>
            </ul>
        </div>
    </div>
</body>
</html>