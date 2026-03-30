<?php
// index.php

// 1. KẾT NỐI DATABASE
$host = 'localhost';
$dbname = 'session_07_miniproject';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kết nối database thất bại: " . $e->getMessage());
}

// 2. LẤY DANH SÁCH CATEGORY CHO DROPDOWN
$categoryStmt = $db->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// 3. NHẬN DỮ LIỆU FILTER TỪ GET
$search = trim($_GET['search'] ?? '');
$categoryId = $_GET['category_id'] ?? '';

// 4. TẠO SQL GỐC
$sql = "SELECT 
            p.id,
            p.name,
            p.price,
            p.stock,
            c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id";

$conditions = [];
$params = [];

// 5. SEARCH THEO TÊN
if ($search !== '') {
    $conditions[] = "p.name LIKE :search";
    $params[':search'] = "%$search%";
}

// 6. FILTER THEO CATEGORY
if ($categoryId !== '' && ctype_digit($categoryId)) {
    $conditions[] = "p.category_id = :category_id";
    $params[':category_id'] = (int)$categoryId;
}

// 7. GHÉP WHERE NẾU CÓ ĐIỀU KIỆN
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// 8. SẮP XẾP
$sql .= " ORDER BY p.id ASC";

// 9. THỰC THI TRUY VẤN
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Product Admin Dashboard</title>
</head>

<body>

    <h1>Product Administration Dashboard</h1>

    <form method="GET" action="">
        <input
            type="text"
            name="search"
            placeholder="Tìm theo tên sản phẩm"
            value="<?= htmlspecialchars($search) ?>">

        <select name="category_id">
            <option value="">-- Tất cả danh mục --</option>
            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= $category['id'] ?>"
                    <?= ($categoryId !== '' && $categoryId == $category['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Lọc</button>
        <a href="index.php" class="reset-btn">Xóa lọc</a>
    </form>

    <?php if (!empty($products)): ?>
        <table>
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
                    <tr class="<?= ($product['stock'] < 10) ? 'low-stock' : '' ?>">
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                        <td class="<?= ($product['stock'] < 10) ? 'low-stock-cell' : '' ?>">
                            <?= htmlspecialchars($product['stock']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Không tìm thấy sản phẩm nào.</p>
    <?php endif; ?>

</body>

</html>