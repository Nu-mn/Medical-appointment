-- Create database 
CREATE DATABASE IF NOT EXISTS invoice_db;
USE invoice_db;

-- Table: invoices
CREATE TABLE IF NOT EXISTS invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_id INT NOT NULL,
    user_id INT NOT NULL,
    fee DECIMAL(12,2) NOT NULL,
    specialization_name VARCHAR(150) NOT NULL,
    patient_name VARCHAR(150) NOT NULL,
    num_order INT NULL,
    status VARCHAR(50) DEFAULT 'Thành công'
);

-- Sample data
INSERT INTO invoices 
    (booking_id, payment_id, user_id, fee, specialization_name, patient_name, num_order, status)
VALUES
    (101, 5001, 1, 250000, 'Nội tổng quát', 'Nguyễn Văn A', 5, 'Thành công'),
    (102, 5002, 1, 120000, 'Tai - Mũi - Họng', 'Nguyễn Văn A', null, 'Thất bại'),
    (103, 5003, 1, 150000, 'Nội tiêu hóa', 'Nguyễn Văn A', 7, 'Thành công'),
    (104, 5004, 2, 100000, 'Da liễu', 'Trần Thị B', null, 'Thất bại');
