CREATE DATABASE IF NOT EXISTS tech_blog;
USE tech_blog;

-- users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- categories
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- tags
CREATE TABLE tags (
    tag_id INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(50) NOT NULL UNIQUE
);

-- posts
CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_users
        FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT fk_posts_categories
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

-- comments
CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_posts
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
    CONSTRAINT fk_comments_users
        FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- post_tags junction table
CREATE TABLE post_tags (
    post_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    CONSTRAINT fk_post_tags_posts
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
    CONSTRAINT fk_post_tags_tags
        FOREIGN KEY (tag_id) REFERENCES tags(tag_id)
);