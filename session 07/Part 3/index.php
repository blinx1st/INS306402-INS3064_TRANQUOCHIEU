<?php
require_once 'Database.php';
$db = Database::getInstance()->getConnection();

?>

<?php
$sql = "Select u.name, u.email, Sum(o.total_amount) as total_spend
    From users u
    Join orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY total_spend DESC
    LIMIT 3";

?>

<?php
$stmt = $db->prepare($sql);
$stmt->execute();
$customers = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customers): ?>
                <tr>
                    <td><?php echo $customers['name']; ?></td>
                    <td><?php echo $customers['email']; ?></td>
                    <td><?php echo $customers['total_spend']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>