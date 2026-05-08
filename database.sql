-- ============================================
-- Smart Attendance System (Edu-Tech) Database
-- ============================================

CREATE DATABASE IF NOT EXISTS edu_tech;
USE edu_tech;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher','admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) UNIQUE NOT NULL,
    branch VARCHAR(50),
    semester INT,
    email VARCHAR(100),
    phone VARCHAR(15),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Teachers Table
CREATE TABLE teachers (
    teacher_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(15),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Subjects Table
CREATE TABLE subjects (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20),
    branch VARCHAR(50),
    semester INT,
    teacher_id INT,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE SET NULL
);

-- Attendance Table
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present','Absent','Late') DEFAULT 'Absent',
    marked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (marked_by) REFERENCES teachers(teacher_id),
    UNIQUE KEY unique_attendance (student_id, subject_id, date)
);

-- Notices Table
CREATE TABLE notices (
    notice_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    created_by INT,
    target_role ENUM('all','student','teacher') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id)
);

-- ============================================
-- SAMPLE DATA (for testing)
-- ============================================

-- Insert admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher'),
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

-- Insert teachers
INSERT INTO teachers (user_id, name, department, email) VALUES
(2, 'Utkarshita Pandey', 'Computer Science', 'utkarshita@itm.edu');

-- Insert students
INSERT INTO students (user_id, name, roll_no, branch, semester, email) VALUES
(3, 'Shiva Giri', 'R001', 'CSE', 3, 'shiva@itm.edu'),
(4, 'Tanay Jaswal', 'R002', 'CSE', 3, 'tanay@itm.edu');

-- Insert subjects
INSERT INTO subjects (subject_name, subject_code, branch, semester, teacher_id) VALUES
('Mathematics', 'MATH301', 'CSE', 3, 1),
('Physics', 'PHY301', 'CSE', 3, 1),
('Chemistry', 'CHEM301', 'CSE', 3, 1),
('Data Structures', 'CS301', 'CSE', 3, 1);

-- Insert sample attendance
INSERT INTO attendance (student_id, subject_id, date, status, marked_by) VALUES
(1, 1, CURDATE(), 'Present', 1),
(1, 2, CURDATE(), 'Present', 1),
(2, 1, CURDATE(), 'Present', 1);

-- Insert sample notices
INSERT INTO notices (title, description, created_by, target_role) VALUES
('Result for S2 out', 'Semester 2 results have been published on the portal.', 1, 'all'),
('Sports Meet Next Week', 'Annual sports meet will be held from Monday to Friday.', 1, 'all'),
('Attendance Shortage Warning', 'Students with below 75% attendance will face detention.', 1, 'student');
