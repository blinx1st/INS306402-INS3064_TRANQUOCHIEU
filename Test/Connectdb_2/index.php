<?php
include("database.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <h2>Welcome to Facebook!</h2>
        Username: <br>
        <input type="text" name="username"><br>
        Password: <br>
        <input type="text" name="password"><br><br>
        <input type="submit" value="Register">
    </form>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username");
    $password = filter_input(INPUT_POST, "password");
    if (empty($username)) {
        echo "Please enter a username";
    } elseif (empty($password)) {
        echo "Please enter a password";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (user, password) VALUES ('$username', '$hash')";
        try {
            mysqli_query($conn, $sql);
            echo "You're now registered!";
        } catch (mysqli_sql_exception) {
            echo "Username already exists. Please choose a different username.";
        }
    }
}

mysqli_close($conn);
?>