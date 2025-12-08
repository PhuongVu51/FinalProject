-- 1. Tạo Database
CREATE DATABASE IF NOT EXISTS teacher_bee_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Sử dụng utf8mb4 để hỗ trợ đa ngôn ngữ và biểu tượng cảm xúc

-- 2. Sử dụng Database
USE teacher_bee_db;

-- 3. Bảng cho Giáo viên (dùng để đăng nhập)
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY, --tự động tăng
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE, 
  `password` VARCHAR(255) NOT NULL,    
  `dob` DATE,                         
  `gender` VARCHAR(10),                 
  `subjects` VARCHAR(255)             
);

-- 4. Bảng Lớp học (Classes)
CREATE TABLE IF NOT EXISTS `classes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `teacher_id` INT DEFAULT NULL,
  UNIQUE KEY `uniq_name_teacher` (`name`, `teacher_id`),
  CONSTRAINT `fk_classes_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE SET NULL
 );

-- 4. Bảng Học sinh (cho chức năng Read/Delete)
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id_code` VARCHAR(50) NOT NULL UNIQUE, -- Mã số HS
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(100),
  `class_name` VARCHAR(50), -- Lớp (legacy)
  `class_id` INT DEFAULT NULL,
  CONSTRAINT `fk_students_class` FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE SET NULL
 );

-- 5. Bảng Bài kiểm tra (cho chức năng Create/Update)
CREATE TABLE IF NOT EXISTS `exams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_title` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(100), -- Môn học
  `exam_date` DATE
 );
