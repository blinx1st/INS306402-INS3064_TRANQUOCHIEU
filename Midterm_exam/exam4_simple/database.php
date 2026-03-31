<?php
// File này chỉ có nhiệm vụ tạo kết nối PDO để các trang CRUD sử dụng.

// Thông tin kết nối MySQL mặc định trong môi trường XAMPP local.
$host = 'localhost';
$dbname = 'hospital_appointment_management';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

try {
    // DSN mô tả kiểu CSDL, host, tên database và charset sẽ dùng.
    $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

    // Tạo đối tượng PDO và bật các option an toàn/cần thiết.
    $pdo = new PDO($dsn, $username, $password, [
        // Báo lỗi bằng exception để dễ try/catch.
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Mặc định fetch dữ liệu dưới dạng mảng kết hợp.
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Tắt giả lập prepared statement để bám sát truy vấn thật.
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $exception) {
    // Ghi log chi tiết cho người phát triển, còn người dùng chỉ thấy lỗi chung.
    error_log('Database connection failed: ' . $exception->getMessage());
    die('Không thể kết nối cơ sở dữ liệu. Hãy kiểm tra XAMPP và import file exam4_hospital.sql.');
}
