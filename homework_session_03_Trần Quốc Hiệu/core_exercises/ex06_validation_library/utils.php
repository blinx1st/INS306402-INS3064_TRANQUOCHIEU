<?php

function sanitize($data): string
{
    $clean = trim((string)$data);
    $clean = stripslashes($clean);
    return htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
}

function validateEmail($email): bool
{
    return filter_var((string)$email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateLength($str, int $min, int $max): bool
{
    $length = strlen((string)$str);
    return $length >= $min && $length <= $max;
}

function validatePassword($pass): bool
{
    $pass = (string)$pass;

    if (!validateLength($pass, 8, 64)) {
        return false;
    }

    return preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'\",.<>\/?\\\\|`~]/', $pass) === 1;
}
