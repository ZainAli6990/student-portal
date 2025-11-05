-- Create database and tables for Student Portal
CREATE DATABASE IF NOT EXISTS student_portal DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE student_portal;


-- users table
CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;


-- Insert a default user: username: admin, password: password123
-- NOTE: change password after import if needed
INSERT INTO users (username, password) VALUES (
'admin',
-- hashed password for 'password123' using PHP's password_hash
'$2y$10$uWmQb6QKq9s1v6l2qh3yDe4gq6GZ3QkR0Pq6r7pTg8J1K0mLz9aC6'
);


-- students table
CREATE TABLE IF NOT EXISTS students (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
email VARCHAR(100) NOT NULL,
course VARCHAR(100) NOT NULL,
photo VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;


-- sample students (photo filenames should match files you'll upload into uploads/)
INSERT INTO students (name, email, course, photo) VALUES
('Ali Khan', 'ali@example.com', 'Computer Science', 'ali.jpg'),
('Riyan Siddiqui', 'riyan@example.com', 'Software Engineering', 'riyan.png'),
('Bilal Ahmed', 'bilal@example.com', 'Information Technology', 'bilal.jpeg');