<?php
// Trang danh sách bệnh nhân.
// Đây là entry point chính của bộ CRUD nhiều trang.
require_once __DIR__ . '/database.php';

// Lấy thông báo trả về sau các thao tác create / edit / delete.
$message = $_GET['message'] ?? '';
$messageText = '';
$messageClass = '';
$errorText = '';
$patients = [];

// Đổi giá trị enum trong database sang nhãn dễ đọc hơn trên giao diện.
$genderLabels = [
    'Male' => 'Nam',
    'Female' => 'Nữ',
    'Other' => 'Khác',
];

// Chuyển mã message trên URL thành nội dung hiển thị.
if ($message === 'created') {
    $messageText = 'Thêm bệnh nhân thành công.';
    $messageClass = 'success';
} elseif ($message === 'updated') {
    $messageText = 'Cập nhật bệnh nhân thành công.';
    $messageClass = 'success';
} elseif ($message === 'deleted') {
    $messageText = 'Xóa bệnh nhân thành công.';
    $messageClass = 'success';
} elseif ($message === 'delete_blocked') {
    $messageText = 'Không thể xóa bệnh nhân vì đã có lịch hẹn liên quan.';
    $messageClass = 'error';
} elseif ($message === 'not_found') {
    $messageText = 'Không tìm thấy bệnh nhân.';
    $messageClass = 'error';
} elseif ($message === 'invalid') {
    $messageText = 'Yêu cầu không hợp lệ.';
    $messageClass = 'error';
}

try {
    // Lấy danh sách bệnh nhân để hiển thị trên bảng.
    $statement = $pdo->query('SELECT id, patient_code, full_name, date_of_birth, gender, phone, address FROM patients ORDER BY id DESC');
    $patients = $statement->fetchAll();
} catch (PDOException $exception) {
    // Nếu truy vấn lỗi thì ghi log và báo lỗi chung trên màn hình.
    error_log('Load patients failed: ' . $exception->getMessage());
    $errorText = 'Không thể tải danh sách bệnh nhân.';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách bệnh nhân</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f4f6f8;
            color: #222;
            margin: 0
        }

        .container {
            max-width: 1100px;
            margin: 32px auto;
            padding: 24px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08)
        }

        h1 {
            margin-top: 0;
            margin-bottom: 20px
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            background: #0d6efd;
            color: #fff
        }

        .btn-edit {
            background: #198754
        }

        .btn-delete {
            background: #dc3545
        }

        .message {
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 16px
        }

        .success {
            background: #e7f6ec;
            color: #146c43
        }

        .error {
            background: #fdeaea;
            color: #b02a37
        }

        .table-wrap {
            overflow-x: auto
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px
        }

        th,
        td {
            border: 1px solid #dcdfe3;
            padding: 10px;
            text-align: left;
            vertical-align: top
        }

        th {
            background: #f0f2f5
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .empty {
            text-align: center;
            color: #666
        }

        @media (max-width:700px) {
            .container {
                margin: 16px;
                padding: 16px
            }

            .btn {
                width: 100%;
                text-align: center
            }

            .top-bar {
                align-items: stretch
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Thanh tiêu đề và nút đi tới trang thêm mới -->
        <div class="top-bar">
            <h1>Danh sách bệnh nhân</h1>
            <a class="btn" href="create.php">Create</a>
        </div>

        <!-- Hiển thị thông báo sau khi thao tác thành công hoặc thất bại -->
        <?php if ($messageText !== ''): ?>
            <div class="message <?= htmlspecialchars($messageClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Báo lỗi nếu không tải được dữ liệu từ database -->
        <?php if ($errorText !== ''): ?>
            <div class="message error"><?= htmlspecialchars($errorText, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Bảng danh sách bệnh nhân -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã bệnh nhân</th>
                        <th>Họ và tên</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Nếu chưa có dữ liệu thì hiển thị dòng trống -->
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td class="empty" colspan="8">Chưa có dữ liệu bệnh nhân.</td>
                        </tr>
                    <?php else: ?>
                        <!-- Duyệt từng bệnh nhân để in ra từng dòng trong bảng -->
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?= (int) $patient['id'] ?></td>
                                <td><?= htmlspecialchars((string) $patient['patient_code'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) $patient['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($patient['date_of_birth'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($genderLabels[$patient['gender']] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($patient['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars((string) ($patient['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <!-- Mỗi nút sẽ chuyển sang một file xử lý khác -->
                                    <div class="actions">
                                        <a class="btn btn-edit" href="edit.php?id=<?= (int) $patient['id'] ?>">Edit</a>
                                        <a class="btn btn-delete" href="delete.php?id=<?= (int) $patient['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bệnh nhân này không?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>