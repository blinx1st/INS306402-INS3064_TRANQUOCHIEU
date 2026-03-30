CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category_id INT NULL,
    stock INT NOT NULL DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Value


-- USERS
INSERT INTO users (id, name, email, created_at) VALUES
(1, 'Nguyen Van An', 'an.nguyen@example.com', '2026-01-05 09:15:00'),
(2, 'Tran Thi Binh', 'binh.tran@example.com', '2026-01-12 14:20:00'),
(3, 'Le Minh Chau', 'chau.le@example.com', '2026-02-01 08:45:00'),
(4, 'Pham Quoc Dat', 'dat.pham@example.com', '2026-02-18 19:10:00'),
(5, 'Hoang Thu Ha', 'ha.hoang@example.com', '2026-03-02 11:30:00');

-- CATEGORIES
INSERT INTO categories (id, category_name) VALUES
(1, 'Electronics'),
(2, 'Fashion'),
(3, 'Books'),
(4, 'Home & Living');

-- PRODUCTS
INSERT INTO products (id, name, price, category_id, stock) VALUES
(1, 'iPhone Charger', 20.00, 1, 50),
(2, 'Wireless Mouse', 25.50, 1, 35),
(3, 'Cotton T-Shirt', 15.00, 2, 100),
(4, 'Denim Jacket', 45.00, 2, 40),
(5, 'SQL for Beginners', 18.99, 3, 60),
(6, 'Coffee Mug', 8.50, 4, 120),
(7, 'Desk Lamp', 32.75, 4, 25),
(8, 'Mystery Box', 12.00, NULL, 15);

-- ORDERS
INSERT INTO orders (id, user_id, order_date, total_amount) VALUES
(1, 1, '2026-03-05 10:15:00', 42.50),
(2, 2, '2026-03-06 14:40:00', 75.00),
(3, 3, '2026-03-08 09:20:00', 51.74),
(4, 1, '2026-03-10 16:05:00', 52.00),
(5, 4, '2026-03-12 20:10:00', 56.97),
(6, 5, '2026-03-15 13:25:00', 69.75);

-- ORDER_ITEMS
INSERT INTO order_items (id, order_id, product_id, quantity, unit_price) VALUES
(1, 1, 2, 1, 25.50),
(2, 1, 6, 2, 8.50),

(3, 2, 3, 2, 15.00),
(4, 2, 4, 1, 45.00),

(5, 3, 5, 1, 18.99),
(6, 3, 7, 1, 32.75),

(7, 4, 1, 2, 20.00),
(8, 4, 8, 1, 12.00),

(9, 5, 5, 3, 18.99),

(10, 6, 7, 2, 32.75),
(11, 6, 6, 1, 8.50),
(12, 6, 8, 1, 12.00);