## Part 1: Normalization

| Table Name  | Primary Key           | Foreign Key         | Normal Form | Description                                                                                                                                                   |
| :---------- | :-------------------- | :------------------ | :---------- | :------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Students    | StudentID             | None                | 3NF         | Stores basic information about students. Each student appears only once to avoid repeating names across many rows.                                            |
| Professors  | ProfessorID           | None                | 3NF         | Stores professor information such as name and email. This prevents repeated professor data in multiple course records.                                        |
| Courses     | CourseID              | ProfessorID         | 3NF         | Stores course details. Each course is linked to the professor who teaches it. This removes repetition of course and professor information.                    |
| Enrollments | (StudentID, CourseID) | StudentID, CourseID | 3NF         | Stores the relationship between students and courses, including the grade obtained. This resolves the many-to-many relationship between students and courses. |

---

# Part 2: Relationships

- **Students to Enrollments:** One-to-Many (1:N)
  Một sinh viên có thể đăng ký nhiều môn học.

- **Courses to Enrollments:** One-to-Many (1:N)
  Một môn học có thể có nhiều sinh viên đăng ký.

- **Students to Courses:** Many-to-Many (N:N)
  Một sinh viên học nhiều môn và một môn có nhiều sinh viên. Quan hệ này được giải quyết thông qua bảng **Enrollments**.

- **Professors to Courses:** One-to-Many (1:N)
  Một giảng viên có thể dạy nhiều môn học, nhưng mỗi môn học chỉ có một giảng viên phụ trách.
