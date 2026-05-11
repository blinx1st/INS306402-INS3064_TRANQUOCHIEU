CREATE DATABASE IF NOT EXISTS testmvc
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE testmvc;

CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    major VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (name, email, phone, major) VALUES
('Tran Quoc Hieu', 'hieu@example.com', '0901234567', 'Information Systems'),
('Nguyen Minh Anh', 'minhanh@example.com', '0912345678', 'Software Engineering')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    phone = VALUES(phone),
    major = VALUES(major);

