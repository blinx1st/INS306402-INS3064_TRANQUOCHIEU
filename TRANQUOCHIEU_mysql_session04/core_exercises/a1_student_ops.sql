-- core_exercises/a1_student_ops.sql

-- Create database with utf8mb4_unicode_ci
CREATE DATABASE IF NOT EXISTS student_management_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE student_management_db;

-- Drop tables for safe re-run (FK-safe order)
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS classes;

-- Table: classes
CREATE TABLE classes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  class_name VARCHAR(100) NOT NULL,
  department VARCHAR(100) NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Table: students
CREATE TABLE students (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_code VARCHAR(30) NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL,
  age INT NULL,
  class_id INT UNSIGNED NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_students_student_code (student_code),
  UNIQUE KEY uq_students_email (email),
  KEY idx_students_class_id (class_id),

  CONSTRAINT fk_students_classes
    FOREIGN KEY (class_id)
    REFERENCES classes (id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;