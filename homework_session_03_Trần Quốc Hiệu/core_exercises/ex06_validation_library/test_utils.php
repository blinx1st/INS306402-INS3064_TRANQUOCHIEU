<?php
require_once __DIR__ . '/utils.php';

function report(string $label, bool $actual, bool $expected): void
{
    $status = ($actual === $expected) ? 'Pass' : 'Fail';
    echo $status . ' - ' . $label . PHP_EOL;
}

echo "=== sanitize() ===" . PHP_EOL;
$sanitized = sanitize("  <b>Hello</b>  ");
report('sanitize trims and escapes HTML', $sanitized === '&lt;b&gt;Hello&lt;/b&gt;', true);

echo PHP_EOL . "=== validateEmail() ===" . PHP_EOL;
report('valid email', validateEmail('admin@example.com'), true);
report('invalid email', validateEmail('admin@@example'), false);

echo PHP_EOL . "=== validateLength() ===" . PHP_EOL;
report('length in range', validateLength('hello', 3, 10), true);
report('length too short', validateLength('hi', 3, 10), false);
report('length too long', validateLength('this is too long', 3, 10), false);

echo PHP_EOL . "=== validatePassword() ===" . PHP_EOL;
report('valid password (length + special char)', validatePassword('Abcdef1!'), true);
report('missing special char', validatePassword('Abcdef12'), false);
report('too short', validatePassword('Ab1!'), false);
