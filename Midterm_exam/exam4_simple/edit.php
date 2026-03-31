<?php
// Trang sửa bệnh nhân.
// File này lấy bệnh nhân theo id, đổ dữ liệu lên form và cập nhật lại khi submit.
require_once __DIR__ . '/database.php';

// Lấy id từ query string. Nếu id sai thì quay về trang danh sách.
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?message=not_found');
    exit;
}

// Danh sách lựa chọn giới tính khớp với enum trong database.
$genderOptions = [
    'Male' => 'Nam',
    'Female' => 'Nữ',
    'Other' => 'Khác',
];

$errors = [];
$generalError = '';

try {
    // Lấy bệnh nhân hiện tại để hiển thị sẵn trên form.
    $patientStatement = $pdo->prepare('SELECT * FROM patients WHERE id = ? LIMIT 1');
    $patientStatement->execute([$id]);
    $patient = $patientStatement->fetch();

    if (!$patient) {
        // Không có bệnh nhân tương ứng với id thì quay về danh sách.
        header('Location: index.php?message=not_found');
        exit;
    }
} catch (PDOException $exception) {
    error_log('Load patient for edit failed: ' . $exception->getMessage());
    die('Không thể tải thông tin bệnh nhân.');
}

$patientCode = (string) $patient['patient_code'];
$fullName = (string) $patient['full_name'];
$dateOfBirth = (string) ($patient['date_of_birth'] ?? '');
$gender = (string) ($patient['gender'] ?? '');
$phone = (string) ($patient['phone'] ?? '');
$address = (string) ($patient['address'] ?? '');

// Khi người dùng submit form cập nhật.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Nhận lại dữ liệu mới từ form.
    $patientCode = trim($_POST['patient_code'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate giống trang create để dữ liệu luôn đúng schema.
    if ($patientCode === '') {
        $errors['patient_code'] = 'Vui lòng nhập mã bệnh nhân.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($patientCode) : strlen($patientCode)) > 20) {
        $errors['patient_code'] = 'Mã bệnh nhân không được quá 20 ký tự.';
    }

    if ($fullName === '') {
        $errors['full_name'] = 'Vui lòng nhập họ và tên.';
    } elseif ((function_exists('mb_strlen') ? mb_strlen($fullName) : strlen($fullName)) > 100) {
        $errors['full_name'] = 'Họ và tên không được quá 100 ký tự.';
    }

    if ($dateOfBirth !== '') {
        $dateObject = DateTime::createFromFormat('Y-m-d', $dateOfBirth);
        if (!$dateObject || $dateObject->format('Y-m-d') !== $dateOfBirth) {
            $errors['date_of_birth'] = 'Ngày sinh không hợp lệ.';
        }
    }

    if ($gender !== '' && !array_key_exists($gender, $genderOptions)) {
        $errors['gender'] = 'Giới tính không hợp lệ.';
    }

    if ($phone !== '' && (function_exists('mb_strlen') ? mb_strlen($phone) : strlen($phone)) > 20) {
        $errors['phone'] = 'Số điện thoại không được quá 20 ký tự.';
    }

    if ($address !== '' && (function_exists('mb_strlen') ? mb_strlen($address) : strlen($address)) > 200) {
        $errors['address'] = 'Địa chỉ không được quá 200 ký tự.';
    }

    if (empty($errors)) {
        try {
            // Kiểm tra trùng mã, nhưng loại trừ chính bệnh nhân hiện tại.
            $checkStatement = $pdo->prepare('SELECT id FROM patients WHERE patient_code = ? AND id <> ? LIMIT 1');
            $checkStatement->execute([$patientCode, $id]);

            if ($checkStatement->fetch()) {
                $errors['patient_code'] = 'Mã bệnh nhân đã tồn tại.';
            } else {
                // Cập nhật dữ liệu bệnh nhân.
                $updateStatement = $pdo->prepare('UPDATE patients SET patient_code = ?, full_name = ?, date_of_birth = ?, gender = ?, phone = ?, address = ? WHERE id = ?');
                $updateStatement->execute([
                    $patientCode,
                    $fullName,
                    $dateOfBirth !== '' ? $dateOfBirth : null,
                    $gender !== '' ? $gender : null,
                    $phone !== '' ? $phone : null,
                    $address !== '' ? $address : null,
                    $id,
                ]);

                // Cập nhật thành công thì quay về trang danh sách.
                header('Location: index.php?message=updated');
                exit;
            }
        } catch (PDOException $exception) {
            // Không show lỗi SQL thô ra giao diện.
            error_log('Update patient failed: ' . $exception->getMessage());
            $generalError = 'Không thể cập nhật bệnh nhân. Vui lòng thử lại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa bệnh nhân</title>
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
            background: #198754;
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
        <h1>Sửa bệnh nhân</h1>

        <!-- Lỗi chung khi cập nhật thất bại -->
        <?php if ($generalError !== ''): ?>
            <div class="message"><?= htmlspecialchars($generalError, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <!-- Form sửa giữ lại dữ liệu cũ hoặc dữ liệu người dùng vừa nhập -->
        <form method="post" action="edit.php?id=<?= $id ?>">
            <div class="form-group">
                <label for="patient_code">Mã bệnh nhân</label>
                <input type="text" id="patient_code" name="patient_code" maxlength="20" value="<?= htmlspecialchars($patientCode, ENT_QUOTES, 'UTF-8') ?>">
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
                <!-- Submit để cập nhật, hoặc quay lại trang danh sách -->
                <button class="btn" type="submit">Cập nhật</button>
                <a class="btn btn-secondary" href="index.php">Quay lại</a>
            </div>
        </form>
    </div>
</body>

</html>