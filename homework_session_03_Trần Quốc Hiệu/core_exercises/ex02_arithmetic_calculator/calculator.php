<?php
$number1 = '';
$number2 = '';
$operation = '+';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $number1 = trim($_POST['number1'] ?? '');
    $number2 = trim($_POST['number2'] ?? '');
    $operation = $_POST['operation'] ?? '+';

    if (!is_numeric($number1) || !is_numeric($number2)) {
        $message = 'Invalid input: both values must be numeric.';
    } else {
        $a = (float)$number1;
        $b = (float)$number2;
        $result = null;

        if ($operation === '+') {
            $result = $a + $b;
        } elseif ($operation === '-') {
            $result = $a - $b;
        } elseif ($operation === '*') {
            $result = $a * $b;
        } elseif ($operation === '/') {
            if ($b == 0.0) {
                $message = 'Error: division by zero is not allowed.';
            } else {
                $result = $a / $b;
            }
        } else {
            $message = 'Invalid operation selected.';
        }

        if ($message === '' && $result !== null) {
            $message = $number1 . ' ' . $operation . ' ' . $number2 . ' = ' . $result;
        }
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
    <title>Arithmetic Calculator</title>
</head>
<body>
    <h1>Arithmetic Calculator</h1>

    <form action="calculator.php" method="post">
        <div>
            <label for="number1">Number 1:</label><br>
            <input type="text" id="number1" name="number1" value="<?php echo esc($number1); ?>">
        </div>
        <br>

        <div>
            <label for="operation">Operation:</label><br>
            <select id="operation" name="operation">
                <option value="+" <?php echo $operation === '+' ? 'selected' : ''; ?>>+</option>
                <option value="-" <?php echo $operation === '-' ? 'selected' : ''; ?>>-</option>
                <option value="*" <?php echo $operation === '*' ? 'selected' : ''; ?>>*</option>
                <option value="/" <?php echo $operation === '/' ? 'selected' : ''; ?>>/</option>
            </select>
        </div>
        <br>

        <div>
            <label for="number2">Number 2:</label><br>
            <input type="text" id="number2" name="number2" value="<?php echo esc($number2); ?>">
        </div>
        <br>

        <button type="submit">Calculate</button>
    </form>

    <?php if ($message !== ''): ?>
        <p><strong><?php echo esc($message); ?></strong></p>
    <?php endif; ?>
</body>
</html>
