<?php

function base_url_path()
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

    return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
}

function url($path = '')
{
    $path = trim($path, '/');
    $base = base_url_path() . '/index.php';

    return $path === '' ? $base : $base . '?url=' . urlencode($path);
}

function asset($path)
{
    return base_url_path() . '/public/' . ltrim($path, '/');
}

function redirect($path = '')
{
    header('Location: ' . url($path));
    exit;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

