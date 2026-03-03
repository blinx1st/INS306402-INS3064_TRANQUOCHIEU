-- advanced_exercises/b2_employee_directory.sql

-- Drop table if exists (safe re-run)
DROP TABLE IF EXISTS employees;

CREATE TABLE employees (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  employee_code VARCHAR(30) NOT NULL,
  full_name VARCHAR(200) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(32) NULL,

  -- ENUM department (predefined values)
  department ENUM(
    'hr',
    'engineering',
    'finance',
    'marketing',
    'sales',
    'operations',
    'it',
    'legal'
  ) NOT NULL,

  position VARCHAR(100) NOT NULL,

  -- Correct HR data types
  hire_date DATE NOT NULL,
  salary DECIMAL(15,2) NOT NULL,

  status ENUM('active', 'inactive', 'on_leave', 'terminated')
    NOT NULL DEFAULT 'active',

  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_employees_employee_code (employee_code),
  UNIQUE KEY uq_employees_email (email),

  CHECK (salary >= 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;