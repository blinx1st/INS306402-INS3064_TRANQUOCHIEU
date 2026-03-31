<?php
// Trang xử lý xóa bệnh nhân.
// File này không có giao diện riêng, chỉ nhận id rồi xóa và redirect về index.php.
require_once __DIR__ . '/database.php';

// Kiểm tra id có hợp lệ hay không.
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?message=invalid');
    exit;
}

try {
    // Xóa bệnh nhân theo id.
    $deleteStatement = $pdo->prepare('DELETE FROM patients WHERE id = ?');
    $deleteStatement->execute([$id]);

    // Nếu không xóa được dòng nào thì có thể id không tồn tại.
    if ($deleteStatement->rowCount() === 0) {
        header('Location: index.php?message=not_found');
        exit;
    }

    // Xóa thành công.
    header('Location: index.php?message=deleted');
    exit;
} catch (PDOException $exception) {
    // Ghi log lỗi để phục vụ debug.
    error_log('Delete patient failed: ' . $exception->getMessage());

    // Lỗi 23000 thường là bị khóa ngoại chặn do bệnh nhân đã có appointments.
    if ($exception->getCode() === '23000') {
        header('Location: index.php?message=delete_blocked');
        exit;
    }

    // Các lỗi còn lại chuyển về thông báo yêu cầu không hợp lệ.
    header('Location: index.php?message=invalid');
    exit;
}
