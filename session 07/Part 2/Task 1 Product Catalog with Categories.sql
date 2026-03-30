SELECT p.*, c.category_name
FROM products as p
Left join categories as c ON c.id = p.category_id;