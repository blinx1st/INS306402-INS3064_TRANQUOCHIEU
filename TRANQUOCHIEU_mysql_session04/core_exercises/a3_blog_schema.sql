-- core_exercises/a3_blog_schema.sql

-- 1) Create database with utf8mb4_unicode_ci
CREATE DATABASE IF NOT EXISTS blog_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE blog_db;

-- 2) Drop tables in FK-safe order (safe re-run)
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 3) users table (UNIQUE username/email, role ENUM, timestamps)
CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin', 'editor', 'author', 'user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 4) categories table (UNIQUE slug)
CREATE TABLE categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 5) posts table (FK author_id -> users.id, category_id -> categories.id)
CREATE TABLE posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  author_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  content LONGTEXT NOT NULL,
  status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_posts_slug (slug),
  KEY idx_posts_author_id (author_id),
  KEY idx_posts_category_id (category_id),

  CONSTRAINT fk_posts_author
    FOREIGN KEY (author_id)
    REFERENCES users (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT fk_posts_category
    FOREIGN KEY (category_id)
    REFERENCES categories (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 6) comments table (FK post_id CASCADE + parent_id self reference)
CREATE TABLE comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  parent_id INT UNSIGNED NULL,
  author_name VARCHAR(150) NULL,
  author_email VARCHAR(255) NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_comments_post_id (post_id),
  KEY idx_comments_user_id (user_id),
  KEY idx_comments_parent_id (parent_id),

  CONSTRAINT fk_comments_post
    FOREIGN KEY (post_id)
    REFERENCES posts (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,

  CONSTRAINT fk_comments_user
    FOREIGN KEY (user_id)
    REFERENCES users (id)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT fk_comments_parent
    FOREIGN KEY (parent_id)
    REFERENCES comments (id)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;