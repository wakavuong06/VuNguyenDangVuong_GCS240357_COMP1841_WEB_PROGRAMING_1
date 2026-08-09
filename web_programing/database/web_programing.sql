-- =====================================================================
--  StudyGrove - database for COMP1841 coursework
--  How to use: phpMyAdmin > Import > choose this file > Go
-- =====================================================================

CREATE DATABASE IF NOT EXISTS web_programing
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE web_programing;

-- Drop child tables first so the foreign keys do not block the import.
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------------------
--  users
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name     VARCHAR(100) NOT NULL,
    username      VARCHAR(30)  NOT NULL,
    email         VARCHAR(120) NOT NULL,
    password      VARCHAR(255) NOT NULL,        -- hashed, never plain text
    role          ENUM('member','admin') NOT NULL DEFAULT 'member',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
--  modules
-- ---------------------------------------------------------------------
CREATE TABLE modules (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code        VARCHAR(10)  NOT NULL,          -- e.g. COMP1841
    name        VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_modules_code (code)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
--  questions
--
--  Two different delete rules on purpose:
--   - if a user is deleted, their questions go too (CASCADE)
--   - a module cannot be deleted while questions still use it (RESTRICT)
-- ---------------------------------------------------------------------
CREATE TABLE questions (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    module_id  INT UNSIGNED NOT NULL,
    title      VARCHAR(150) NOT NULL,
    body       TEXT         NOT NULL,
    image      VARCHAR(120) NULL DEFAULT NULL,  -- optional screenshot
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_questions_user (user_id),
    KEY idx_questions_module (module_id),
    CONSTRAINT fk_questions_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_questions_module
        FOREIGN KEY (module_id) REFERENCES modules (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- ---------------------------------------------------------------------
--  messages - what the contact form saves
-- ---------------------------------------------------------------------
CREATE TABLE messages (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    sender_name  VARCHAR(100) NOT NULL,
    sender_email VARCHAR(120) NOT NULL,
    subject      VARCHAR(150) NOT NULL,
    body         TEXT         NOT NULL,
    email_sent   TINYINT(1)   NOT NULL DEFAULT 0,  -- did the e-mail go out?
    is_read      TINYINT(1)   NOT NULL DEFAULT 0,  -- 0 until the admin opens the inbox
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================================
--  Demo data
--  Logins:  admin123    / admin123
--           thao.nguyen / Member@123
--           minh.tran   / Member@123
--           ha.le       / Member@123
-- =====================================================================

INSERT INTO users (id, full_name, username, email, password, role, created_at) VALUES
(1, 'Administrator', 'admin123', 'admin@studygrove.local',
 '$2y$10$c5doKdK1iPNs5r5U3a0ILeHFv3avxZ7Bldps.nJPIN4f7NO0jZp1y', 'admin', '2026-07-01 09:00:00'),
(2, 'Thao Nguyen', 'thao.nguyen', 'thao.nguyen@example.com',
 '$2y$10$nMgrr4K3ejhW4MaTbKZ6GeLggweCOH64MGpW5ZkA4ZL8rd/m5JsAK', 'member', '2026-07-02 10:15:00'),
(3, 'Minh Tran', 'minh.tran', 'minh.tran@example.com',
 '$2y$10$nMgrr4K3ejhW4MaTbKZ6GeLggweCOH64MGpW5ZkA4ZL8rd/m5JsAK', 'member', '2026-07-03 14:40:00'),
(4, 'Ha Le', 'ha.le', 'ha.le@example.com',
 '$2y$10$nMgrr4K3ejhW4MaTbKZ6GeLggweCOH64MGpW5ZkA4ZL8rd/m5JsAK', 'member', '2026-07-05 08:25:00');

INSERT INTO modules (id, code, name, description) VALUES
(1, 'COMP1841', 'Web Programming 1', 'PHP, MySQL and the MVC pattern.'),
(2, 'COMP1773', 'User Interface Design', 'Personas, wireframes and prototyping.'),
(3, 'COMP1752', 'Object Oriented Programming', 'Classes and objects in Python.'),
(4, 'COMP1639', 'Database Engineering', 'Relational design and SQL.'),
(5, 'COMP1549', 'Advanced Programming', 'Java, networking and design patterns.');

INSERT INTO questions (id, user_id, module_id, title, body, image, created_at) VALUES
(1, 2, 1,
 'PDOException: invalid parameter number - what am I doing wrong?',
 'I am preparing a statement with a named placeholder :id, but when I call execute() I get "SQLSTATE[HY093] Invalid parameter number".\n\nI have attached a screenshot of the code and the error. Do the array key and the placeholder name have to be identical?',
 'img_seed_pdo.png', '2026-07-08 09:12:00'),

(2, 3, 1,
 'Difference between require, require_once and include in PHP?',
 'The lecture used require_once for config.php but include for the view files. They all pull in another file, so when should I choose which one? And what happens if the file is missing - a warning or a fatal error?',
 NULL, '2026-07-09 15:47:00'),

(3, 4, 2,
 'Text overflows outside its card - what is the CSS fix?',
 'One of my product cards has a very long word in the name and it escapes the card border (screenshot attached). I tried width:100% on the paragraph but it still spills over.\n\nIs there a property that forces a long word to wrap inside its container?',
 'img_seed_css.png', '2026-07-11 11:05:00'),

(4, 2, 4,
 'When is a table in 3NF? My design has a repeating city column',
 'For the coursework database I have a customers table that stores city and city_postcode together. A friend says this breaks third normal form because postcode depends on city, not on the customer. Is that right?',
 NULL, '2026-07-14 19:30:00'),

(5, 3, 3,
 'Why use __init__ instead of setting attributes afterwards?',
 'In Python I can write p = Person() and then p.name = "Minh" afterwards, and it works. So what is the benefit of defining __init__ with parameters? Is it only style, or does it prevent real bugs?',
 NULL, '2026-07-16 08:55:00'),

(6, 4, 1,
 'Do I need to escape data before INSERT if I use prepared statements?',
 'I read that htmlspecialchars() protects against XSS and prepared statements protect against SQL injection. Are both needed? Should I call htmlspecialchars() before saving, or only when printing the value back into the page?',
 NULL, '2026-07-19 13:20:00'),

(7, 2, 5,
 'Client-server chat in Java: one thread per client or a thread pool?',
 'For the group project we must build a small chat server. The examples online either start a new Thread for every socket or use an ExecutorService pool. For about 20 clients, which is more appropriate?',
 NULL, '2026-07-21 16:42:00');

INSERT INTO messages (id, sender_name, sender_email, subject, body, email_sent, is_read, created_at) VALUES
(1, 'Lan Pham', 'lan.pham@example.com',
 'Suggestion: dark mode for late-night study',
 'Hi StudyGrove team, the site is really helpful before exams. Could you add a dark colour scheme? Reading light backgrounds at 1 a.m. is hard on the eyes. Thank you!',
 0, 0, '2026-07-20 23:41:00');