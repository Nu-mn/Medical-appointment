-- Create database
CREATE DATABASE IF NOT EXISTS invoice_db;
USE invoice_db;

-- Table: invoices
CREATE TABLE IF NOT EXISTS invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    payment_id INT NOT NULL,
    user_id INT NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    issued_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table: invoice_detail
CREATE TABLE IF NOT EXISTS invoice_detail (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    cost DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(invoice_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO invoices (booking_id, payment_id, user_id, total_amount, issued_date)
VALUES
(101, 5001, 1, 450000, '2025-11-12 08:30:00'),
(102, 5002, 1, 820000, '2025-11-12 09:15:00'),
(103, 5003, 1, 1200000, '2025-11-14 14:20:00'); 

INSERT INTO invoice_detail (invoice_id, service_name, cost)
VALUES
(1, 'Khám tổng quát', 300000),
(1, 'Xét nghiệm máu cơ bản', 150000);

INSERT INTO invoice_detail (invoice_id, service_name, cost)
VALUES
(2, 'Khám Tai - Mũi - Họng', 250000),
(2, 'Nội soi mũi', 350000),
(2, 'Thuốc điều trị 3 ngày', 220000);

INSERT INTO invoice_detail (invoice_id, service_name, cost)
VALUES
(3, 'Khám nội tổng quát', 300000),
(3, 'Siêu âm bụng tổng quát', 400000),
(3, 'Xét nghiệm nước tiểu', 150000),
(3, 'Xét nghiệm máu nâng cao', 350000);
