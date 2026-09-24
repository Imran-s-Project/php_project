-- Exam Website Database Schema
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL DEFAULT 30,
    negative_marking DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    pass_percentage INT NOT NULL DEFAULT 40,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(500) NOT NULL,
    option_b VARCHAR(500) NOT NULL,
    option_c VARCHAR(500) NOT NULL,
    option_d VARCHAR(500) NOT NULL,
    correct_option CHAR(1) NOT NULL,
    explanation TEXT,
    marks INT NOT NULL DEFAULT 1,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    exam_id INT NOT NULL,
    total_questions INT NOT NULL DEFAULT 0,
    correct_count INT NOT NULL DEFAULT 0,
    wrong_count INT NOT NULL DEFAULT 0,
    unanswered_count INT NOT NULL DEFAULT 0,
    score DECIMAL(6,2) NOT NULL DEFAULT 0,
    max_score INT NOT NULL DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option CHAR(1) NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (attempt_id) REFERENCES attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin login -> username: admin / password: admin123  (CHANGE AFTER FIRST LOGIN)
INSERT INTO admins (username, password) VALUES
('admin', '$2b$12$znpcw9/KCJSKjSUsbvZKZepTZZ9ibeCbVsa0BssNRkhiW4mNuZjKK');

-- Sample exam + questions so the site works immediately after import
INSERT INTO exams (title, description, duration_minutes, negative_marking, pass_percentage, is_active) VALUES
('সাধারণ জ্ঞান - নমুনা পরীক্ষা', 'একটি নমুনা exam যা দিয়ে সিস্টেমটি টেস্ট করতে পারবেন।', 10, 0.25, 40, 1);

INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, marks) VALUES
(1, 'বাংলাদেশের রাজধানীর নাম কী?', 'ঢাকা', 'চট্টগ্রাম', 'খুলনা', 'রাজশাহী', 'A', 'ঢাকা বাংলাদেশের রাজধানী ও বৃহত্তম শহর।', 1),
(1, '2 + 2 x 2 = ?', '4', '6', '8', '2', 'B', 'গাণিতিক নিয়মে আগে গুণ হয়: 2 + (2x2) = 6।', 1),
(1, 'পানির রাসায়নিক সংকেত কী?', 'CO2', 'O2', 'H2O', 'NaCl', 'C', 'পানি দুটি হাইড্রোজেন ও একটি অক্সিজেন পরমাণু দিয়ে গঠিত (H2O)।', 1);
