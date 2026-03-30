SELECT 
    c.category_name,
    SUM(oi.quantity * oi.unit_price) AS total_revenue
FROM categories c
JOIN products p 
    ON c.id = p.category_id
JOIN order_items oi 
    ON p.id = oi.product_id
GROUP BY c.category_name;