<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number1 = trim($_POST['number1'] ?? '');
    $number2 = trim($_POST['number2'] ?? '');
    $operation = $_POST['operation'] ?? '+';
    if (!is_numeric($number1) || !is_numeric($number2)) {
        echo "<p style='color: red;'>Error: both inputs must be numeric.</p>";
    } else {
        $result = 0;
        if ($operation === '+') {
            $result = $number1 + $number2;
        } elseif ($operation === '-') {
            $result = $number1 - $number2;
        } elseif ($operation === '*') {
            $result = $number1 * $number2;
        } elseif ($operation === '/') {
            if ($number2 == 0) {
                echo "<p style='color: red;'>Error: division by zero is not allowed.</p>";
            } else {
                $result = $number1 / $number2;
            }
        } else {
            echo "<p style='color: red;'>Error: invalid operation selected.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Calculator</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <div>
            <label for="n1">Number 1</label>
            <input type="text" name="number1" id="n1">
        </div>
        <div>
            <label for="operation">Operation:</label>
            <select name="operation" id="operation">
                <option value="+">+</option>
                <option value="-">-</option>
                <option value="*">*</option>
                <option value="/">/</option>
            </select>
        </div>
        <div>
            <label for="n2">Number 2</label>
            <input type="text" name="number2" id="n2">
        </div>
        <div>
            <button type="submit">Calculate</button>
        </div><label for="n1"></label>
    </form>
</body>
<?php
echo "<p><b>Result: $result</b></p>";
?>

</html>