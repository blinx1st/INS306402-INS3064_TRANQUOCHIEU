Part 1:

1. **INNER JOIN vs LEFT JOIN**
   An **INNER JOIN** only returns rows where both tables have a matching record.
   A **LEFT JOIN** returns **all rows from the left table**, and if there is no match in the right table, the right-side columns will be **NULL**.

2. **Purpose of HAVING**
   The **HAVING** clause is used to filter results **after grouping**, especially when using aggregate functions like `SUM()` or `COUNT()`.
   We cannot use **WHERE** for this because `WHERE` works **before** `GROUP BY` and before aggregates are calculated.

3. **PDO Definition**
   **PDO** stands for **PHP Data Objects**.
   Two advantages of PDO over older `mysqli` are:

- It supports **multiple database systems** (MySQL, PostgreSQL, SQLite, etc.)
- It provides a cleaner and safer way to use **prepared statements**

4. **How Prepared Statements prevent SQL Injection**
   Prepared statements separate the SQL code from the user input.
   The query is sent first with placeholders like `?` or `:name`, then the user data is bound separately. This means the input is treated only as **data**, not as part of the SQL command, so malicious SQL cannot be executed.

5. **Execution order of WHERE, GROUP BY, HAVING**
   The typical order is:

**WHERE → GROUP BY → HAVING**

- `WHERE`: filters rows first
- `GROUP BY`: groups the remaining rows
- `HAVING`: filters the grouped results afterward
