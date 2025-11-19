-- Create database
CREATE DATABASE IF NOT EXISTS doctor_db;
USE doctor_db;

-- Table: specializations
CREATE TABLE IF NOT EXISTS specializations (
    specialization_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    amount DECIMAL(12,2) NOT NULL  
);

-- Table: doctors
CREATE TABLE IF NOT EXISTS doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    gender ENUM('Nam', 'Nữ', 'Khác') NOT NULL,
    specialization_id INT NOT NULL,
    experience_years INT DEFAULT 0,
    description TEXT,
    status INT DEFAULT 1,
    FOREIGN KEY (specialization_id) REFERENCES specializations(specialization_id)
);

-- Table: doctor_schedule
CREATE TABLE IF NOT EXISTS doctor_schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    date DATE NOT NULL,
    session ENUM('morning', 'afternoon', 'evening') NOT NULL,
    available_slots INT DEFAULT 0,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Sample data
INSERT INTO specializations (name, amount)
VALUES
('Tim mạch', 180000),
('Sản - Phụ khoa', 150000),
('Tai - Mũi - Họng', 120000),
('Da liễu', 130000),
('Nội tổng quát', 100000),
('Ngoại tổng quát', 160000);

-- Tim mạch (ID=1)
INSERT INTO doctors (doctor_name, email, gender, specialization_id, experience_years, description, status) 
VALUES
('BS Nguyễn Văn A', 'vana@hospital.com', 'Nam', 1, 10, 'Chuyên gia tim mạch cấp cao', 1),
('BS Trần Thị B', 'thib@hospital.com', 'Nữ', 1, 7, 'Điều trị suy tim và tăng huyết áp', 1),
('BS Lê Quốc C', 'quo cc@hospital.com', 'Nam', 1, 5, 'Bác sĩ tim mạch trẻ, chuyên mạch vành', 1);

-- Sản - Phụ khoa (ID=2)
INSERT INTO doctors (doctor_name, email, gender, specialization_id, experience_years, description, status)
VALUES
('BS Phạm Thị D', 'thid@hospital.com', 'Nữ', 2, 12, 'Bác sĩ sản khoa nhiều kinh nghiệm', 1),
('BS Nguyễn Hồng E', 'honge@hospital.com', 'Nữ', 2, 8, 'Khám thai và sinh thường', 1),
('BS Võ Thanh F', 'thanhf@hospital.com', 'Nam', 2, 6, 'Chuyên phẫu thuật sản', 1);

-- Tai - Mũi - Họng (ID=3)
INSERT INTO doctors (doctor_name, email, gender, specialization_id, experience_years, description, status)
VALUES
('BS Đinh Văn G', 'vang@hospital.com', 'Nam', 3, 9, 'Điều trị viêm tai và viêm xoang', 1),
('BS Lê Mỹ H', 'myh@hospital.com', 'Nữ', 3, 4, 'Bác sĩ TMH tổng quát', 1),
('BS Hoàng Phúc I', 'phuci@hospital.com', 'Nam', 3, 11, 'Phẫu thuật nội soi TMH', 1);

-- Da liễu (ID=4)
INSERT INTO doctors (doctor_name, email, gender, specialization_id, experience_years, description, status)
VALUES
('BS Trịnh Kim J', 'kimj@hospital.com', 'Nữ', 4, 10, 'Điều trị da liễu thẩm mỹ', 1),
('BS Ngô Thái K', 'thaik@hospital.com', 'Nam', 4, 7, 'Điều trị mụn và viêm da', 1),
('BS Phạm Mỹ L', 'myl@hospital.com', 'Nữ', 4, 5, 'Chăm sóc da chuyên sâu', 1);

-- Nội tổng quát (ID=5)
INSERT INTO doctors (doctor_name, email, gender, specialization_id, experience_years, description, status)
VALUES
('BS Nguyễn Minh M', 'minhm@hospital.com', 'Nam', 5, 15, 'Nội khoa tổng quát', 1),
('BS Võ Thanh N', 'thanhn@hospital.com', 'Nam', 5, 6, 'Điều trị bệnh mạn tính', 1),
('BS Đặng Mỹ O', 'myo@hospital.com', 'Nữ', 5, 9, 'Khám nội cơ bản', 1);

-- Ngoại tổng quát (ID=6)
INSERT INTO doctors (doctor_name, email, gender, specialization_id, experience_years, description, status)
VALUES
('BS Phan Hữu P', 'huup@hospital.com', 'Nam', 6, 14, 'Phẫu thuật ngoại khoa', 1),
('BS Đỗ Minh Q', 'minhq@hospital.com', 'Nam', 6, 8, 'Ngoại tiêu hóa', 1),
('BS Trương Mỹ R', 'myr@hospital.com', 'Nữ', 6, 6, 'Ngoại tổng quát', 1);


INSERT INTO doctor_schedule (doctor_id, date, session, available_slots)
VALUES
(1, '2025-11-15', 'Sáng', 10),
(1, '2025-11-16', 'Chiều', 8),
(1, '2025-11-18', 'Tối', 0),             -- FULL SLOT

(2, '2025-11-17', 'Sáng', 12),
(2, '2025-11-18', 'Chiều', 10),
(2, '2025-11-20', 'Tối', 0),             -- FULL SLOT

(11, '2025-11-15', 'Sáng', 6),
(11, '2025-11-19', 'Sáng', 5),
(11, '2025-11-21', 'Chiều', 0),           -- FULL SLOT

(4, '2025-11-20', 'Sáng', 0),            -- FULL SLOT
(4, '2025-11-21', 'Chiều', 0),           -- FULL SLOT
(4, '2025-11-22', 'Tối', 4),

(5, '2025-11-15', 'Sáng', 15),
(5, '2025-11-22', 'Chiều', 9),
(5, '2025-11-23', 'Tối', 0),            -- FULL SLOT

(6, '2025-11-15', 'Sáng', 10),
(6, '2025-11-16', 'Chiều', 8),
(6, '2025-11-18', 'Tối', 0),             -- FULL SLOT

(9, '2025-11-17', 'Sáng', 12),
(9, '2025-11-18', 'Chiều', 10),
(9, '2025-11-20', 'Tối', 0),             -- FULL SLOT

(13, '2025-11-15', 'Sáng', 6),
(13, '2025-11-19', 'Sáng', 5),
(13, '2025-11-21', 'Chiều', 0),           -- FULL SLOT

(16, '2025-11-20', 'Sáng', 0),            -- FULL SLOT
(16, '2025-11-21', 'Chiều', 0),           -- FULL SLOT
(16, '2025-11-22', 'Tối', 4),

(18, '2025-11-15', 'Sáng', 15),
(18, '2025-11-22', 'Chiều', 9),
(18, '2025-11-23', 'Tối', 0);