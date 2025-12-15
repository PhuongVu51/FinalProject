-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 15, 2025 lúc 03:20 PM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `teacher_bee_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `class_id`, `status`, `applied_at`) VALUES
(1, 4, 1, 'approved', '2025-12-09 04:42:13'),
(2, 2, 2, 'approved', '2025-12-09 04:45:14'),
(3, 2, 1, 'pending', '2025-12-09 05:19:02'),
(4, 2, 3, 'pending', '2025-12-09 05:23:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `classes`
--

INSERT INTO `classes` (`id`, `name`, `teacher_id`) VALUES
(1, 'MIS2023B', 2),
(2, 'Math 101', 1),
(3, 'Math01', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_title` varchar(255) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT 45,
  `exam_type` enum('quiz','essay','mixed') DEFAULT 'quiz',
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exams`
--

INSERT INTO `exams` (`id`, `exam_title`, `subject`, `exam_date`, `end_date`, `duration`, `exam_type`, `class_id`, `teacher_id`) VALUES
(1, 'English Midterm', 'English', '2025-02-12', '2025-02-13 00:00:00', 45, 'quiz', 1, 2),
(2, 'Math Final', 'Math', '2025-12-15', '2025-12-16 00:00:00', 45, 'quiz', 2, 1),
(3, 'English', 'Eng', '2025-12-09', '2025-12-10 00:00:00', 45, 'quiz', 3, 2),
(4, 'rng', 'Eng', '2025-12-09', '2025-12-10 00:00:00', 45, 'quiz', 3, 2),
(5, 'Math - Midterm', 'General', '2025-12-13', '2025-12-14 00:00:00', 45, 'quiz', 3, 2),
(6, 'English', 'General', '2025-12-14', '2025-12-15 00:00:00', 45, 'quiz', 3, 2),
(7, 'Math 2', 'General', '2025-12-14', '2025-12-15 00:00:00', 45, 'quiz', 3, 2),
(8, 'BCH', 'General', '2025-12-15', '2025-12-16 00:00:00', 45, 'quiz', 1, 2),
(9, 'English 3', 'bài kiểm tra', '2025-12-13', '2025-12-14 21:40:00', 5, 'mixed', 1, 2),
(10, 'Test', NULL, '2025-12-13', '2025-12-13 21:56:00', 5, 'mixed', 1, 2),
(11, 'Midterm Exam', NULL, '2025-12-14', '2025-12-14 07:45:00', 45, 'mixed', 1, 2),
(12, 'Midterm', 'MIS', '2025-12-15', NULL, 45, 'quiz', 3, 2),
(13, 'mis', 'Eng', '2025-12-16', NULL, 45, 'quiz', 3, 2),
(14, 'Mis', 'MIS', '2025-12-16', NULL, 45, 'quiz', 1, 2),
(15, 'English', NULL, '2025-12-15', '2025-12-15 14:45:00', 45, 'quiz', 1, 2),
(16, '123456', NULL, '2025-12-15', '2025-12-15 15:15:00', 45, 'quiz', 1, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exam_options`
--

CREATE TABLE `exam_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exam_options`
--

INSERT INTO `exam_options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, 'A', 0),
(2, 1, 'C', 1),
(3, 1, 'B', 0),
(4, 1, 'D', 0),
(5, 2, '1', 0),
(6, 2, '3', 0),
(7, 2, '2', 1),
(8, 2, '4', 0),
(9, 3, '1', 1),
(10, 3, '3', 0),
(11, 3, '2', 0),
(12, 3, '4', 0),
(13, 4, '1', 0),
(14, 4, '3', 0),
(15, 4, '2', 0),
(16, 4, '4', 1),
(17, 5, 'A', 1),
(18, 5, 'C', 0),
(19, 5, 'B', 0),
(20, 5, 'FD', 0),
(21, 6, '1', 1),
(22, 6, '2', 0),
(23, 6, '3', 0),
(24, 6, '4', 0),
(25, 7, '1', 0),
(26, 7, '3', 1),
(27, 7, '2', 0),
(28, 7, '4', 0),
(29, 8, '1', 0),
(30, 8, '2', 0),
(31, 8, '3', 0),
(32, 8, '4', 1),
(33, 10, '1', 1),
(34, 10, '3', 0),
(35, 10, '2', 0),
(36, 10, '4', 0),
(37, 11, '1', 1),
(38, 11, '3', 0),
(39, 11, '2', 0),
(40, 11, '4', 0),
(41, 13, 'B2B', 1),
(42, 13, 'D2D', 0),
(43, 13, 'C2C', 0),
(44, 13, 'D2B', 0),
(45, 14, 'a', 1),
(46, 14, 'c', 0),
(47, 14, 'b', 0),
(48, 14, 'd', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_type` enum('multiple_choice','essay') DEFAULT 'multiple_choice',
  `question_text` text NOT NULL,
  `points` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `question_type`, `question_text`, `points`) VALUES
(1, 6, 'multiple_choice', 'abc', 1),
(2, 6, 'multiple_choice', 'A', 1),
(3, 7, 'multiple_choice', 'abc', 1),
(4, 7, 'multiple_choice', 'hâhha', 1),
(5, 8, 'multiple_choice', 'Ai là', 1),
(6, 9, 'multiple_choice', 'Ai là', 1),
(7, 9, 'multiple_choice', 'a', 1),
(8, 9, 'multiple_choice', 'B', 1),
(9, 10, 'essay', 'abc?', 1),
(10, 10, 'multiple_choice', 'abc', 1),
(11, 11, 'multiple_choice', 'abc', 1),
(12, 11, 'essay', 'tại sao?', 1),
(13, 15, 'multiple_choice', 'What is B2B?', 1),
(14, 16, 'multiple_choice', 'abc', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Chào mừng năm học mới', 'Hệ thống CourseMS chào đón các bạn học sinh...', '2025-12-09 04:42:13'),
(2, 'Lịch thi học kỳ 1', 'Các bạn học sinh chú ý lịch thi toán vào ngày 15...', '2025-12-09 04:42:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `score` float DEFAULT 0,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `scores`
--

INSERT INTO `scores` (`id`, `exam_id`, `student_id`, `score`, `comments`) VALUES
(1, 1, 1, 10, 'Excellent'),
(2, 1, 2, 9, 'Good job'),
(3, 1, 3, 9, 'Well done'),
(4, 2, 4, 7.5, 'Keep trying'),
(17, 1, 4, 10, NULL),
(18, 8, 1, 9, NULL),
(19, 8, 3, 2, NULL),
(20, 8, 4, 0, NULL),
(21, 9, 1, 10, NULL),
(22, 9, 3, 10, NULL),
(23, 9, 4, 10, NULL),
(24, 10, 1, 10, NULL),
(25, 10, 3, 10, NULL),
(26, 10, 4, 10, NULL),
(57, 11, 1, 10, NULL),
(61, 15, 1, 10, NULL),
(62, 15, 3, 10, NULL),
(63, 15, 4, 10, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_code` varchar(50) NOT NULL,
  `class_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_code`, `class_id`) VALUES
(1, 4, '23070123', 1),
(2, 5, '123123', 2),
(3, 6, '251267', 1),
(4, 7, '23070125', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `email`) VALUES
(1, 1, '23070553@vnu.edu.vn'),
(2, 2, 'mp@lms.com'),
(3, 8, 'ntl@cms.com'),
(4, 9, 'abc@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL DEFAULT 'student',
  `full_name` varchar(150) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `full_name`, `avatar`, `remember_token`, `created_at`) VALUES
(1, '23070553@vnu.edu.vn', 'e10adc3949ba59abbe56e057f20f883e', 'teacher', 'Nguyen Minh Thuy', NULL, NULL, '2025-12-09 04:42:13'),
(2, 'mp@lms.com', 'e10adc3949ba59abbe56e057f20f883e', 'teacher', 'Vũ Thị Minh Phương', NULL, '73fe2fd6145f21ae63dd1e0820522da6', '2025-12-09 04:42:13'),
(3, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'admin', 'Super Admin', NULL, '78eb6a4cb6488420e9c4a58935593f2a', '2025-12-09 04:42:13'),
(4, '23070123', 'e10adc3949ba59abbe56e057f20f883e', 'student', 'Nguyễn Minh Thuý', NULL, NULL, '2025-12-09 04:42:13'),
(5, '123123', 'e10adc3949ba59abbe56e057f20f883e', 'student', 'Pham Ha Chi', NULL, NULL, '2025-12-09 04:42:13'),
(6, '251267', 'e10adc3949ba59abbe56e057f20f883e', 'student', 'Nguyễn Minh Thuý (B)', NULL, NULL, '2025-12-09 04:42:13'),
(7, '23070125', 'e10adc3949ba59abbe56e057f20f883e', 'student', 'Phung Duc Duy', NULL, NULL, '2025-12-09 04:42:13'),
(8, 'ntl@cms.com', 'e10adc3949ba59abbe56e057f20f883e', 'teacher', 'Nguyễn Thuỳ Linh', NULL, NULL, '2025-12-09 04:52:19'),
(9, 'abc@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'teacher', 'abc', NULL, NULL, '2025-12-09 05:22:23');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Chỉ mục cho bảng `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Chỉ mục cho bảng `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Chỉ mục cho bảng `exam_options`
--
ALTER TABLE `exam_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Chỉ mục cho bảng `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_score` (`exam_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Chỉ mục cho bảng `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_code` (`student_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Chỉ mục cho bảng `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Chỉ mục cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `exam_options`
--
ALTER TABLE `exam_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT cho bảng `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT cho bảng `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `exam_options`
--
ALTER TABLE `exam_options`
  ADD CONSTRAINT `exam_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `fk_sa_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sa_question` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sa_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
