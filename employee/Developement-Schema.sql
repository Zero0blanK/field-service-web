
CREATE TABLE `employee`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `contact_number` VARCHAR(20) NOT NULL,
    `role` VARCHAR(255) NOT NULL,
    `status` ENUM('Present', 'Absent', 'On-Leave') NOT NULL,
    `pay` DECIMAL(10, 2) NOT NULL,
    `days_of_work` INT NOT NULL
);

CREATE TABLE `customer`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `contact_number` VARCHAR(20) NOT NULL,
    `address` TEXT NOT NULL
);

CREATE TABLE `appointment`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT UNSIGNED NULL,
    `date` DATE NOT NULL,
    `category` VARCHAR(255) NOT NULL,
    `priority` ENUM('Low', 'Medium', 'High', 'Urgent') NOT NULL,
    `status` ENUM('Pending', 'Working', 'Completed', 'Cancelled') NOT NULL
);

CREATE TABLE `quotation`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `appointment_id` INT UNSIGNED NULL,
    `amount` DECIMAL(10, 2) NOT NULL
);

CREATE TABLE `pending_collection`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `billing_statement_id` INT UNSIGNED NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('Pending', 'Paid') NOT NULL
);

CREATE TABLE `employee_log`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT UNSIGNED NULL,
    `appointment_id` INT UNSIGNED NULL
);

CREATE TABLE `customer_feedback`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `appointment_id` INT UNSIGNED NULL,
    `feedback` TEXT NOT NULL
);

CREATE TABLE `service_report`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT UNSIGNED NULL,
    `amount` DECIMAL(10, 2) NOT NULL
);

CREATE TABLE `quotation_data` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT UNSIGNED NULL,
    `data` JSON NOT NULL
);

CREATE TABLE `service_report_data` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `service_report_id` INT UNSIGNED NULL,
    `data` JSON NOT NULL
);

CREATE TABLE `billing_statement`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quotation_id` INT UNSIGNED NULL,
    `service_report_id` INT UNSIGNED NULL,
    `amount` DECIMAL(10, 2) NOT NULL
);

-- Foreign Keys (Relaxed, Allow NULLs, Removed Constraints for Easier Testing)
ALTER TABLE `appointment`
    ADD CONSTRAINT `appointment_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer`(`id`) ON DELETE SET NULL;

ALTER TABLE `quotation`
    ADD CONSTRAINT `quotation_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointment`(`id`) ON DELETE SET NULL;

ALTER TABLE `service_report`
    ADD CONSTRAINT `service_report_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotation`(`id`) ON DELETE SET NULL;

ALTER TABLE `billing_statement`
    ADD CONSTRAINT `billing_statement_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotation`(`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `billing_statement_service_report_id_foreign` FOREIGN KEY (`service_report_id`) REFERENCES `service_report`(`id`) ON DELETE SET NULL;

ALTER TABLE `employee_log`
    ADD CONSTRAINT `employee_log_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee`(`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `employee_log_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointment`(`id`) ON DELETE SET NULL;

ALTER TABLE `customer_feedback`
    ADD CONSTRAINT `customer_feedback_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointment`(`id`) ON DELETE SET NULL;

ALTER TABLE `pending_collection`
    ADD CONSTRAINT `pending_collection_billing_statement_id_foreign` FOREIGN KEY (`billing_statement_id`) REFERENCES `billing_statement`(`id`) ON DELETE SET NULL;

ALTER TABLE `quotation_data`
    ADD CONSTRAINT `quotation_data_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotation`(`id`) ON DELETE SET NULL;

ALTER TABLE `service_report_data`
    ADD CONSTRAINT `service_report_data_service_report_id_foreign` FOREIGN KEY (`service_report_id`) REFERENCES `service_report`(`id`) ON DELETE SET NULL;
