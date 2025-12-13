CREATE TABLE teachers (
  id int(11) NOT NULL AUTO_INCREMENT,
  full_name varchar(150) NOT NULL,
  email varchar(100) NOT NULL UNIQUE,
  password varchar(255) NOT NULL,
  dob date DEFAULT NULL,
  gender varchar(10) DEFAULT NULL,
  subjects varchar(255) DEFAULT NULL,
  role_id int(11) DEFAULT 2,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table 2: classes
-- ============================================
CREATE TABLE classes (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(150) NOT NULL,
  teacher_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY fk_classes_teacher (teacher_id),
  CONSTRAINT fk_classes_teacher FOREIGN KEY (teacher_id) REFERENCES teachers (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table 3: students
-- ============================================
CREATE TABLE students (
  id int(11) NOT NULL AUTO_INCREMENT,
  student_id_code varchar(50) NOT NULL UNIQUE,
  full_name varchar(150) NOT NULL,
  email varchar(100) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  class_id int(11) DEFAULT NULL,
  class_name varchar(150) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY fk_students_class (class_id),
  CONSTRAINT fk_students_class FOREIGN KEY (class_id) REFERENCES classes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table 4: exams
-- ============================================
CREATE TABLE exams (
  id int(11) NOT NULL AUTO_INCREMENT,
  exam_title varchar(255) NOT NULL,
  subject varchar(100) DEFAULT NULL,
  exam_date date DEFAULT NULL,
  class_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY fk_exams_class (class_id),
  CONSTRAINT fk_exams_class FOREIGN KEY (class_id) REFERENCES classes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table 5: scores
-- ============================================
CREATE TABLE scores (
  id int(11) NOT NULL AUTO_INCREMENT,
  exam_id int(11) NOT NULL,
  student_id int(11) NOT NULL,
  score float NOT NULL,
  comments text DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY unique_exam_student (exam_id, student_id),
  KEY fk_scores_exam (exam_id),
  KEY fk_scores_student (student_id),
  CONSTRAINT fk_scores_exam FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE,
  CONSTRAINT fk_scores_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;