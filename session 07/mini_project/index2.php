<?php
$host = 'localhost';
$dbname = 'product_admin';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Lấy categories cho dropdown
$categorySql = "SELECT id, name FROM categories ORDER BY name ASC";
$categoryStmt = $db->prepare($categorySql);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// Lấy dữ liệu filter
$search = trim($_GET['search'] ?? '');
$categoryId = $_GET['category_id'] ?? '';

// SQL gốc
$sql = "SELECT 
            p.id,
            p.name,
            p.price,
            c.name AS category_name,
            p.stock
        FROM products p
        JOIN categories c ON p.category_id = c.id";

$conditions = [];
$params = [];

// Search theo tên
if ($search !== '') {
    $conditions[] = "p.name LIKE :search";
    $params[':search'] = "%$search%";
}

// Filter theo category
if ($categoryId !== '') {
    $conditions[] = "p.category_id = :category_id";
    $params[':category_id'] = $categoryId;
}

// Ghép WHERE
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY p.id ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Product Admin</title>
</head>

<body>
    <h1>Product Administration Dashboard</h1>

    <form method="GET" action="">
        <input
            type="text"
            name="search"
            placeholder="Search product name..."
            value="<?= htmlspecialchars($search) ?>">

        <select name="category_id">
            <option value="">-- All Categories --</option>
            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= $category['id'] ?>"
                    <?= ($categoryId == $category['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category Name</th>
                <th>Stock Level</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= number_format($product['price'], 2) ?></td>
                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                    <td><?= htmlspecialchars($product['stock']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>