CREATE DATABASE IF NOT EXISTS staff_management;

USE staff_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS staff_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    department VARCHAR(150) NOT NULL,
    position VARCHAR(150) NOT NULL
);

INSERT INTO users (email, password)
VALUES (
    'admin@example.com',
    '$2y$12$dai3RNCoWBrZhE/ntAoON.Y1PkRgf7TT27qeu5omysj7iOWNW1Mdu'
)
ON DUPLICATE KEY UPDATE
    password = VALUES(password);

INSERT INTO staff_members (first_name, last_name, email, department, position)
VALUES
    ('Jane', 'Doe', 'jane.doe@example.com', 'Operations', 'Operations Manager'),
    ('John', 'Smith', 'john.smith@example.com', 'Human Resources', 'HR Coordinator')
ON DUPLICATE KEY UPDATE
    first_name = VALUES(first_name),
    last_name = VALUES(last_name),
    department = VALUES(department),
    position = VALUES(position);
