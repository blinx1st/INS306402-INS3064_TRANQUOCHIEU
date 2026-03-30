<?php
require_once "database.php";
$db = Database::getInstance()->getConnection();
?><?php
    // Lấy từ khóa tìm kiếm
    $search = trim($_GET['search'] ?? '');
    $filter = trim($_GET['category_id'] ?? '');
    // SQL có JOIN
    $sql = "SELECT 
            p.id,
            p.product_name,
            p.price,
            c.category_name AS category_name,
            p.stock
        FROM products p
        JOIN categories c ON p.category_id = c.id";

    // Nếu có search thì thêm điều kiện LIKE
    $params = [];
    $conditions = [];

    if ($search !== '') {
        $conditions[] = "p.product_name LIKE :search";
        $params[':search'] = "%$search%";
    }

    if ($filter !== '') {
        $conditions[] = "p.category_id = :category_id";
        $params[':category_id'] = $filter;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY p.id ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .low-stock-row {
        color: #ff0000;
    }
</style>

<body>
    <h1>Product List</h1>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Stock</th>
            </tr>
        </thead>
        <thbody><?php
                foreach ($products as $product): ?><tr class="<?php if ($product['stock'] < 10) {
                                                                    echo 'low-stock-row';
                                                                } ?>">
                    <td><?php echo $product['id'] ?></td>
                    <td><?php echo $product['product_name'] ?></td>
                    <td><?php echo $product['price'] ?></td>
                    <td><?php echo $product['category_name'] ?></td>
                    <td><?php echo $product['stock'] ?></td>
                </tr><?php endforeach; ?></thbody>
    </table>
    <!--Dynamic Search -->
    <form method="GET" action=""><input type="text"
            name="search"
            placeholder="Search products..."
            value="<?php echo htmlspecialchars($search); ?>"><Select name="category_id">
            <option value="">All Categories</option>
            <option value="<?php echo htmlspecialchars('1'); ?>">Điện thoại</option>
            <option value="<?php echo htmlspecialchars('2'); ?>">Laptop</option>
            <option value="<?php echo htmlspecialchars('3'); ?>">Phụ kiện</option>
            <option value="<?php echo htmlspecialchars('4'); ?>">Thiết bị văn phòng</option>
        </Select><button type="submit">Search</button></form>
</body>

</html>