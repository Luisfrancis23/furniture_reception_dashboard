-- Timor Furniture Co. Receptionist Dashboard Database Dump
-- Location: C:\xampp\htdocs\furniture_reception_dashboard\database.sql

CREATE DATABASE IF NOT EXISTS `timor_furniture_reception` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `timor_furniture_reception`;

-- 1. Table structure for visitors
CREATE TABLE IF NOT EXISTS `visitors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `visitor_id` VARCHAR(30) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(100),
  `purpose` VARCHAR(100) NOT NULL,
  `person_to_meet` VARCHAR(100) NOT NULL,
  `check_in` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `check_out` DATETIME NULL,
  `status` VARCHAR(20) DEFAULT 'Checked In',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table structure for appointments
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(100),
  `appointment_date` DATE NOT NULL,
  `appointment_time` TIME NOT NULL,
  `purpose` VARCHAR(150) NOT NULL,
  `sales_rep` VARCHAR(100) NOT NULL,
  `status` VARCHAR(30) DEFAULT 'Scheduled',
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table structure for customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_code` VARCHAR(30) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `company_name` VARCHAR(100),
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(100),
  `address` VARCHAR(200) NOT NULL,
  `city` VARCHAR(50) DEFAULT 'Dili',
  `purchase_history` TEXT,
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table structure for showroom_visits
CREATE TABLE IF NOT EXISTS `showroom_visits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `visitor_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `visit_date` DATE NOT NULL,
  `furniture_interest` VARCHAR(100) NOT NULL,
  `assigned_rep` VARCHAR(100) NOT NULL,
  `status` VARCHAR(30) DEFAULT 'In Showroom',
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table structure for notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(30) DEFAULT 'info',
  `is_read` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table structure for settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) UNIQUE NOT NULL,
  `setting_value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Data
INSERT INTO `visitors` (`visitor_id`, `full_name`, `phone`, `email`, `purpose`, `person_to_meet`, `check_in`, `check_out`, `status`) VALUES
('TL-VIS-1001', 'Joao Pereira', '+670 7712 3456', 'joao.p@gov.tl', 'Executive Teak Table Consultation', 'Manuel Santos (Senior Sales Manager)', '2026-07-27 09:15:00', NULL, 'Checked In'),
('TL-VIS-1002', 'Maria da Costa', '+670 7823 4567', 'maria.costa@telecom.tl', 'Living Room Sofa & Dining Set Tour', 'Filomena Soares (Interior Designer)', '2026-07-27 10:00:00', '2026-07-27 11:30:00', 'Checked Out'),
('TL-VIS-1003', 'Carlos Belo', '+670 7799 8811', 'cbelo@construction.tl', 'Custom Office Desk Supplier Inquiry', 'Antonio Guterres (Procurement Rep)', '2026-07-27 11:15:00', NULL, 'Checked In');

INSERT INTO `customers` (`customer_code`, `full_name`, `company_name`, `phone`, `email`, `address`, `city`, `purchase_history`, `notes`) VALUES
('TL-CUST-501', 'Ministry of Infrastructure', 'Gov Timor-Leste', '+670 331 2345', 'info@infrastructure.gov.tl', 'Avenida de Portugal, Dili', 'Dili', 'Purchased 15 Executive Mahogany Desks & 30 Swivel Chairs ($24,500)', 'VIP Government Client'),
('TL-CUST-502', 'Timor Plaza Hotel & Suites', 'Timor Plaza Group', '+670 332 9988', 'management@timorplaza.tl', 'Rua de Comoro, Dili', 'Dili', 'Purchased 50 Hotel Suite Bedroom Sets ($68,000)', 'Recurring commercial account');

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'Timor Teak Furniture Co. Lda'),
('company_phone', '+670 331 8888'),
('company_email', 'reception@timorfurniture.tl'),
('company_address', 'Avenida de Portugal, Farol, Dili, Timor-Leste'),
('business_hours', 'Mon - Sat: 08:00 AM - 05:30 PM'),
('receptionist_name', 'Maria Silva'),
('receptionist_role', 'Lead Receptionist & Guest Coordinator');
