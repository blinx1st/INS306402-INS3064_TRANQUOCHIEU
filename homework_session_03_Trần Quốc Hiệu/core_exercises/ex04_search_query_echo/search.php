<?php
$query = $_GET['q'] ?? '';

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
    <title>Search</title>
</head>
<body>
    <h1>Search</h1>

    <form action="search.php" method="get">
        <label for="q">Keyword:</label>
        <input type="text" id="q" name="q" value="<?php echo esc($query); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($query !== ''): ?>
        <p>You searched for: <strong><?php echo esc($query); ?></strong></p>
    <?php endif; ?>
</body>
</html>
