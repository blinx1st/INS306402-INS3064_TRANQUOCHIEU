-- core_exercises/a2_shop_inventory.sql

-- Drop table if exists (safe re-run)
DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_name VARCHAR(200) NOT NULL,
  sku VARCHAR(50) NOT NULL,
  price DECIMAL(15,2) NOT NULL,
  stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  -- Business rule: SKU must be unique
  UNIQUE KEY uq_products_sku (sku),

  -- Business rule: price must be strictly positive
  CHECK (price > 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;