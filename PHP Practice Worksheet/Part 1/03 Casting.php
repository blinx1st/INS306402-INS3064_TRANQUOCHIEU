<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $str = "25.50";
    $floatVal = (float)$str;

    $intVal = (int)$floatVal;

    // Print result using gettype()
    echo gettype($floatVal) . "(" . $floatVal . "), ";
    echo gettype($intVal) . "(" . $intVal . ")";
    ?>
</body>
</html>
