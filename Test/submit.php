<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $fullName = trim($_POST['full_name'] ?? 'error');
    echo "<h2>Hello, $fullName!</h2>";
    ?>
</body>

</html>