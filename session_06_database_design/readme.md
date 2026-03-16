## Part 1: Normalization

# Task 1:

| Table Name  | Primary Key           | Foreign Key         | Normal Form | Description                                                                                                                                                   |
| :---------- | :-------------------- | :------------------ | :---------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Students    | StudentID             | None                | 3NF         | Stores basic information about students. Each student appears only once to avoid repeating names across many rows.                                            |
| Professors  | ProfessorID           | None                | 3NF         | Stores professor information such as name and email. This prevents repeated professor data in multiple course records.                                        |
| Courses     | CourseID              | ProfessorID         | 3NF         | Stores course details. Each course is linked to the professor who teaches it. This removes repetition of course and professor information.                    |
| Enrollments | (StudentID, CourseID) | StudentID, CourseID | 3NF         | Stores the relationship between students and courses, including the grade obtained. This resolves the many-to-many relationship between students and courses. |

---

Task 2 — Decompose to 3NF

- **Students to Enrollments:** One-to-Many (1:N)
  Một sinh viên có thể đăng ký nhiều môn học.

- **Courses to Enrollments:** One-to-Many (1:N)
  Một môn học có thể có nhiều sinh viên đăng ký.

- **Students to Courses:** Many-to-Many (N:N)
  Một sinh viên học nhiều môn và một môn có nhiều sinh viên. Quan hệ này được giải quyết thông qua bảng **Enrollments**.

- **Professors to Courses:** One-to-Many (1:N)
  Một giảng viên có thể dạy nhiều môn học, nhưng mỗi môn học chỉ có một giảng viên phụ trách.

## Part 2: Relationship Drills

Dựa theo **quy tắc ERD cơ bản**:

> Nếu **A có thể liên kết với nhiều B**, thì **Foreign Key đặt ở phía nhiều (many side)**.

Dưới đây là câu trả lời theo **định dạng Markdown**.

---

# Part 2: Relationships

| Relationship       | Relationship Type  | FK Location                             | Explanation                                                                           |
| ------------------ | ------------------ | --------------------------------------- | ------------------------------------------------------------------------------------- |
| Author — Book      | One-to-Many (1:N)  | Book.author_id                          | Một tác giả có thể viết nhiều cuốn sách, nhưng mỗi cuốn sách chỉ có một tác giả.      |
| Citizen — Passport | One-to-One (1:1)   | Passport.citizen_id                     | Một công dân chỉ có một hộ chiếu và mỗi hộ chiếu thuộc về một công dân.               |
| Customer — Order   | One-to-Many (1:N)  | Order.customer_id                       | Một khách hàng có thể đặt nhiều đơn hàng, nhưng mỗi đơn hàng thuộc về một khách hàng. |
| Student — Class    | Many-to-Many (N:N) | Enrollment table (student_id, class_id) | Một sinh viên có thể học nhiều lớp và một lớp có nhiều sinh viên.                     |
| Team — Player      | One-to-Many (1:N)  | Player.team_id                          | Một đội có nhiều cầu thủ, nhưng mỗi cầu thủ thuộc một đội.                            |

---

# Relationship Summary (ngắn gọn kiểu bài thi)

- **Author → Book:** 1:N → FK ở **Book**
- **Citizen → Passport:** 1:1 → FK ở **Passport**
- **Customer → Order:** 1:N → FK ở **Order**
- **Student ↔ Class:** N:N → cần **junction table (Enrollment)**
- **Team → Player:** 1:N → FK ở **Player**

---

# Ví dụ SQL cho 1 case (Customer – Order)

```sql
CREATE TABLE Customers (
    customer_id INT PRIMARY KEY,
    customer_name VARCHAR(100)
);

CREATE TABLE Orders (
    order_id INT PRIMARY KEY,
    order_date DATE,
    customer_id INT,
    FOREIGN KEY (customer_id) REFERENCES Customers(customer_id)
);
```
