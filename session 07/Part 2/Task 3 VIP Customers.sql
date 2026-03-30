SELECT u.id, u.name, u.email,
	SUM(o.total_amount) AS total_spend
FROM users u 
    JOIN orders o ON u.id = o.user_id
    GROUP BY u.id, u.name, u.email
    ORDER BY total_spend DESC;
    LITMIT 3