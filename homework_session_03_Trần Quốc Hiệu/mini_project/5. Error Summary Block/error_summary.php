<?php
// error_summary.php

// Initialize an array to hold error messages
$errors = [];

// Example validation logic (you can customize this)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
        $errors[] = "Name is required.";
    }
    if (empty($_POST["email"])) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    // Add more validation as needed
}

// Display errors if there are any
if (!empty($errors)) {
    echo '<div class="alert alert-danger">';
    echo '<strong>Error!</strong> Please fix the following issues:<br>';
    foreach ($errors as $error) {
        echo '- ' . htmlspecialchars($error) . '<br>';
    }
    echo '</div>';
}

// Highlight invalid fields (example for name and email)
$nameClass = isset($_POST["name"]) && empty($_POST["name"]) ? 'is-invalid' : '';
$emailClass = isset($_POST["email"]) && (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) ? 'is-invalid' : '';
?>

<form method="post" action="">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control <?php echo $nameClass; ?>" id="name" name="name" value="<?php echo isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control <?php echo $emailClass; ?>" id="email" name="email" value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ''; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>