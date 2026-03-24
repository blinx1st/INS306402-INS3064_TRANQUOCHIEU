<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Dữ liệu từ POST:</h3>";
    print_r($_POST);
} else {
    echo "<h3>Dữ liệu từ GET:</h3>";
    print_r($_GET);
}
?>

<form method="get">
    <input type="text" name="username" placeholder="Nhập tên">
    <button type="submit">Gửi bằng GET</button>
</form>

<form method="post">
    <input type="text" name="username" placeholder="Nhập tên">
    <button type="submit">Gửi bằng POST</button>
</form>