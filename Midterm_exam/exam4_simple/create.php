<?php
// Trang thêm bệnh nhân mới.
// File này vừa hiển thị form, vừa xử lý submit tạo dữ liệu.
require_once __DIR__ . '/database.php';

// Danh sách lựa chọn giới tính khớp với enum trong database.
$genderOptions = [
    'Male' => 'Nam',
    'Female' => 'Nữ',
    'Other' => 'Khác',
];

// Biến lưu dữ liệu form để giữ lại giá trị khi validate lỗi.
$patientCode = '';
$fullName = '';
$dateOfBirth = '';
$gender = '';
$phone = '';
$address = '';
$errors = [];
$generalError = '';

// Chỉ xử lý khi người dùng bấm submit form.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Lấy dữ liệu người dùng nhập từ form.
    $patientCode = trim($_POST['patient_code'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate mã bệnh nhân: bắt buộc và không vượt quá độ dài trong schema.
    // function_exists được dùng để kiểm tra xem hàm mb_strlen có tồn tại hay không, vì một số môi trường PHP có thể không cài đặt phần mở rộng mbstring. Nếu mb_strlen có sẵn, nó sẽ được sử dụng để tính độ dài chuỗi hỗ trợ Unicode, ngược lại sẽ fallback về strlen truyền thống.
    //- mb_strlen dung để hỗ trợ đúng độ dài chuỗi có ký tự Unicode, fallback về strlen nếu mb_strlen không có sẵn.
    if ($patientCode === '') {
        $errors['patient_code'] = 'Vui lòng nhập mã bệnh nhân.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($patientCode) : strlen($patientCode)) > 20) {
        $errors['patient_code'] = 'Mã bệnh nhân không được quá 20 ký tự.';
    }

    // Validate họ tên: bắt buộc và không vượt quá độ dài trong schema.
    if ($fullName === '') {
        $errors['full_name'] = 'Vui lòng nhập họ và tên.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($fullName) : strlen($fullName)) > 100) {
        $errors['full_name'] = 'Họ và tên không được quá 100 ký tự.';
    }

    // Nếu có nhập ngày sinh thì kiểm tra đúng định dạng Y-m-d.
    if ($dateOfBirth !== '') {
        $dateObject = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
        if (!$dateObject || $dateObject->format('Y-m-d') !== $dateOfBirth) {
            $errors['date_of_birth'] = 'Ngày sinh không hợp lệ.';
        }
    }

    // Chỉ chấp nhận giá trị giới tính có trong danh sách cho phép.
    if ($gender !== '' && !array_key_exists($gender, $genderOptions)) {
        $errors['gender'] = 'Giới tính không hợp lệ.';
    }

    // Kiểm tra độ dài số điện thoại và địa chỉ để khớp schema.
    if ($phone !== '' && (function_exists('mb_strlen') ? mb_strlen($phone) : strlen($phone)) > 20) {
        $errors['phone'] = 'Số điện thoại không được quá 20 ký tự.';
    }

    if ($address !== '' && (function_exists('mb_strlen') ? mb_strlen($address) : strlen($address)) > 200) {
        $errors['address'] = 'Địa chỉ không được quá 200 ký tự.';
    }

    // Chỉ thao tác database khi không còn lỗi validate.
    if (empty($errors)) {
        try {
            // Kiểm tra mã bệnh nhân đã tồn tại hay chưa để tránh trùng unique key.
            $checkStatement = $pdo->prepare('SELECT id FROM patients WHERE patient_code = ? LIMIT 1');
            $checkStatement->execute([$patientCode]);

            if ($checkStatement->fetch()) {
                $errors['patient_code'] = 'Mã bệnh nhân đã tồn tại.';
            } else {
                // Thêm bản ghi mới vào bảng patients.
                $insertStatement = $pdo->prepare('INSERT INTO patients (patient_code, full_name, date_of_birth, gender, phone, address) VALUES (?, ?, ?, ?, ?, ?)');
                $insertStatement->execute([
                    $patientCode,
                    $fullName,
                    $dateOfBirth !== '' ? $dateOfBirth : null,
                    $gender !== '' ? $gender : null,
                    $phone !== '' ? $phone : null,
                    $address !== '' ? $address : null,
                ]);

                // Thêm thành công thì quay về trang danh sách.
                header('Location: index.php?message=created');
                exit;
            }
        } catch (PDOException $exception) {
            // Không hiển thị lỗi SQL thô ra màn hình.
            error_log('Create patient failed: ' . $exception->getMessage());
            $generalError = 'Không thể thêm bệnh nhân. Vui lòng thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm bệnh nhân</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f4f6f8;
            color: #222;
            margin: 0
        }

        .container {
            max-width: 760px;
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

        .message {
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
            background: #fdeaea;
            color: #b02a37
        }

        .form-group {
            margin-bottom: 16px
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cfd4da;
            border-radius: 6px;
            font: inherit
        }

        textarea {
            min-height: 100px;
            resize: vertical
        }

        .error-text {
            color: #b02a37;
            font-size: 14px;
            margin-top: 6px
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            background: #0d6efd;
            color: #fff;
            cursor: pointer;
            font: inherit
        }

        .btn-secondary {
            background: #6c757d
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
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Thêm bệnh nhân mới</h1>

        <!-- Lỗi chung khi thao tác database thất bại -->
        <?php if ($generalError !== ''): ?>
            <div class="message"><?= htmlspecialchars($generalError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Form nhập thông tin bệnh nhân -->
        <form method="post">
            <div class="form-group">
                <label for="patient_code">Mã bệnh nhân</label>
                <input type="text" id="patient_code" name="patient_code" maxlength="20" value="<?= htmlspecialchars($patientCode, ENT_QUOTES, 'UTF-8') ?>">
                <!-- Lỗi riêng của trường mã bệnh nhân -->
                <?php if (!empty($errors['patient_code'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['patient_code'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="full_name">Họ và tên</label>
                <input type="text" id="full_name" name="full_name" maxlength="100" value="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Ngày sinh</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($dateOfBirth, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['date_of_birth'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['date_of_birth'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="gender">Giới tính</label>
                <select id="gender" name="gender">
                    <option value="">-- Chọn giới tính --</option>
                    <?php foreach ($genderOptions as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= $gender === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['gender'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" maxlength="20" value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address" maxlength="200"><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (!empty($errors['address'])): ?>
                    <div class="error-text"><?= htmlspecialchars($errors['address'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="actions">
                <!-- Nút Lưu để submit, nút Quay lại để trở về danh sách -->
                <button class="btn" type="submit">Lưu</button>
                <a class="btn btn-secondary" href="index.php">Quay lại</a>
            </div>
        </form>
    </div>
</body>

</html>