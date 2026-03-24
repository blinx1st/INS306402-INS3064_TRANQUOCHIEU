<?php
$errors = [];
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($name)) {
        $errors['name'] = 'Tên không được để trống.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email không được để trống.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không hợp lệ.';
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        .error-box {
            color: red;
            border: 1px solid red;
            padding: 10px;
        }

        .input-error {
            border: 2px solid red;
        }
    </style>
</head>

<body>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <strong>Có lỗi xảy ra:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Tên"
            class="<?= isset($errors['name']) ? 'input-error' : '' ?>">

        <br><br>

        <input type="text" name="email" placeholder="Email"
            class="<?= isset($errors['email']) ? 'input-error' : '' ?>">

        <br><br>

        <button type="submit">Submit</button>
    </form>

</body>

</html>