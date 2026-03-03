-- advanced_exercises/b1_library_design.sql

-- (Optional) create a database if you want to run it standalone
-- CREATE DATABASE IF NOT EXISTS library_db
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;
-- USE library_db;

-- Drop in FK-safe order (optional for re-runs)
DROP TABLE IF EXISTS borrow_records;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS members;

-- 1) books
-- ISBN-13: store as VARCHAR (may contain leading zeros, no arithmetic needed)
CREATE TABLE books (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  isbn_13 VARCHAR(13) NOT NULL,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(200) NULL,
  publisher VARCHAR(200) NULL,
  published_year YEAR NULL,
  total_copies INT UNSIGNED NOT NULL DEFAULT 1,
  available_copies INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_books_isbn_13 (isbn_13),
  CHECK (CHAR_LENGTH(isbn_13) = 13),
  CHECK (available_copies <= total_copies)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 2) members
-- Phone: store as VARCHAR for international formats (+84...), spaces, leading zeros
CREATE TABLE members (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  member_code VARCHAR(30) NOT NULL,
  full_name VARCHAR(200) NOT NULL,
  email VARCHAR(255) NULL,
  phone VARCHAR(32) NOT NULL,
  address VARCHAR(255) NULL,
  joined_at DATE NOT NULL DEFAULT (CURRENT_DATE),

  PRIMARY KEY (id),
  UNIQUE KEY uq_members_member_code (member_code),
  UNIQUE KEY uq_members_email (email),
  UNIQUE KEY uq_members_phone (phone)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 3) borrow_records
-- Dates: borrow_date, due_date, return_date (nullable)
-- References both books and members
CREATE TABLE borrow_records (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  book_id INT UNSIGNED NOT NULL,
  member_id INT UNSIGNED NOT NULL,
  borrow_date DATE NOT NULL DEFAULT (CURRENT_DATE),
  due_date DATE NOT NULL,
  return_date DATE NULL,
  status ENUM('borrowed', 'returned', 'overdue') NOT NULL DEFAULT 'borrowed',
  notes VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_borrow_records_book_id (book_id),
  KEY idx_borrow_records_member_id (member_id),
  KEY idx_borrow_records_status (status),

  CONSTRAINT fk_borrow_records_book
    FOREIGN KEY (book_id)
    REFERENCES books (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT fk_borrow_records_member
    FOREIGN KEY (member_id)
    REFERENCES members (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CHECK (due_date >= borrow_date),
  CHECK (return_date IS NULL OR return_date >= borrow_date)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;