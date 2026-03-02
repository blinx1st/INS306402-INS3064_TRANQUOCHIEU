<?php
// method_toggle.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>POST Method</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    echo "<h2>GET Method</h2>";
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GET vs POST Toggle</title>
</head>

<body>
    <h1>GET vs POST Toggle</h1>

    <form method="GET" action="">
        <label for="getInput">GET Input:</label>
        <input type="text" name="getInput" id="getInput">
        <button type="submit">Submit GET</button>
    </form>

    <form method="POST" action="">
        <label for="postInput">POST Input:</label>
        <input type="text" name="postInput" id="postInput">
        <button type="submit">Submit POST</button>
    </form>
</body>

</html>