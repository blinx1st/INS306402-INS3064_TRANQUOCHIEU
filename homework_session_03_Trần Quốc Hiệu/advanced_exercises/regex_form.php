<?php
$password = $_POST['password'] ?? '';
$errors = [];

if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Thiếu chữ in hoa.';
}

if (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Thiếu chữ thường.';
}

if (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Thiếu số.';
}

if (!preg_match('/[\W_]/', $password)) {
    $errors[] = 'Thiếu ký tự đặc biệt.';
}

if (empty($errors)) {
    echo "Mật khẩu hợp lệ.";
} else {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
}
