SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS hospital_appointment_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE hospital_appointment_management;

DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS patients;

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender ENUM('Male', 'Female', 'Other') DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address VARCHAR(200) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    appointment_date DATETIME NOT NULL,
    department VARCHAR(100) NOT NULL,
    reason TEXT DEFAULT NULL,
    status ENUM('Scheduled', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
    CONSTRAINT fk_appointments_patients
        FOREIGN KEY (patient_id) REFERENCES patients(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO patients (patient_code, full_name, date_of_birth, gender, phone, address) VALUES
('PT001', 'Nguyen Van An', '2001-04-15', 'Male', '0901234567', '12 Le Loi, District 1, Ho Chi Minh City'),
('PT002', 'Tran Thi Bich Ngoc', '1999-09-22', 'Female', '0912345678', '45 Nguyen Trai, District 5, Ho Chi Minh City'),
('PT003', 'Le Quoc Bao', '1988-01-11', 'Male', '0987654321', '89 Tran Hung Dao, Ninh Kieu, Can Tho'),
('PT004', 'Pham Thu Ha', '1995-07-03', 'Female', '0934567890', '21 Hai Ba Trung, Hue'),
('PT005', 'Vo Minh Khang', '2003-12-19', 'Male', '0977123456', '77 Cach Mang Thang 8, Da Nang'),
('PT006', 'Doan Gia Linh', '1997-06-27', 'Female', '0943216789', '38 Phan Chu Trinh, Hoi An'),
('PT007', 'Bui Hoang Nam', '1985-02-05', 'Male', '0966543210', '15 Nguyen Du, Hai Phong'),
('PT008', 'Hoang My Duyen', '2000-10-30', 'Female', '0923456789', '66 Quang Trung, Da Lat'),
('PT009', 'Dang Tuan Kiet', '1993-03-09', 'Male', '0909988776', '142 Ly Thuong Kiet, Bien Hoa'),
('PT010', 'Truong Khanh Vy', '2002-08-14', 'Female', '0911223344', '5 Vo Van Tan, Vung Tau');

INSERT INTO appointments (patient_id, doctor_name, appointment_date, department, reason, status) VALUES
(1, 'Dr. Nguyen Minh Chau', '2026-03-20 08:30:00', 'Cardiology', 'Routine heart check-up after mild chest pain.', 'Completed'),
(1, 'Dr. Tran Gia Phuc', '2026-04-08 14:00:00', 'General Medicine', 'Follow-up consultation and blood pressure review.', 'Scheduled'),
(2, 'Dr. Le Hoang Yen', '2026-03-25 09:15:00', 'Dermatology', 'Skin allergy consultation.', 'Completed'),
(3, 'Dr. Pham Quoc Huy', '2026-04-01 10:00:00', 'Orthopedics', 'Knee pain after playing football.', 'Scheduled'),
(4, 'Dr. Nguyen Thanh Mai', '2026-03-18 13:30:00', 'ENT', 'Persistent sore throat and cough.', 'Completed'),
(5, 'Dr. Vo Anh Tuan', '2026-03-29 15:45:00', 'Neurology', 'Recurring migraine headaches.', 'Completed'),
(6, 'Dr. Do Thi Minh Anh', '2026-04-03 11:20:00', 'Gynecology', 'Regular health screening.', 'Scheduled'),
(7, 'Dr. Bui Duc Long', '2026-03-27 16:10:00', 'Ophthalmology', 'Blurry vision examination.', 'Completed'),
(8, 'Dr. Hoang Khanh Linh', '2026-04-05 08:00:00', 'Nutrition', 'Diet consultation for weight management.', 'Scheduled'),
(8, 'Dr. Tran Van Duc', '2026-03-12 09:50:00', 'General Medicine', 'Fever and fatigue consultation.', 'Cancelled');
