
SET FOREIGN_KEY_CHECKS = 0;  

CREATE TABLE `employee` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `contact_number` VARCHAR(20) NOT NULL,
    `role` VARCHAR(255) NOT NULL,
    `status` ENUM('Present', 'Absent', 'On-Leave') NOT NULL,
    `pay` DECIMAL(10, 2) NOT NULL,
    `days_of_work` INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `customer` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `contact_number` VARCHAR(20) NOT NULL,
    `address` TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `appointment` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `category` VARCHAR(255) NOT NULL,		
    `priority` ENUM('Low', 'Medium', 'High', 'Urgent') NOT NULL,
    `status` ENUM('Pending', 'Working', 'Completed', 'Cancelled') NOT NULL,
    FOREIGN KEY (`customer_id`) REFERENCES `customer`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `quotation` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `appointment_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointment`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `service_report` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`quotation_id`) REFERENCES `quotation`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `billing_statement` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT UNSIGNED NOT NULL,
    `service_report_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (`quotation_id`) REFERENCES `quotation`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_report_id`) REFERENCES `service_report`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `pending_collection` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `billing_statement_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('Pending', 'Paid') NOT NULL,
    FOREIGN KEY (`billing_statement_id`) REFERENCES `billing_statement`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `employee_log` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT UNSIGNED NOT NULL,
    `appointment_id` INT UNSIGNED,  -- Allow NULL to prevent foreign key errors
    FOREIGN KEY (`employee_id`) REFERENCES `employee`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointment`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE `customer_feedback` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `appointment_id` INT UNSIGNED NOT NULL,
    `feedback` TEXT NOT NULL,
    FOREIGN KEY (`appointment_id`) REFERENCES `appointment`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `quotation_data` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT UNSIGNED NOT NULL UNIQUE,
    `data` JSON NOT NULL,
    FOREIGN KEY (`quotation_id`) REFERENCES `quotation`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `service_report_data` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `service_report_id` INT UNSIGNED NOT NULL UNIQUE,
    `data` JSON NOT NULL,
    FOREIGN KEY (`service_report_id`) REFERENCES `service_report`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Enable foreign key checks again
SET FOREIGN_KEY_CHECKS = 1;