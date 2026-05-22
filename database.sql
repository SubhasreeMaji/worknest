CREATE DATABASE IF NOT EXISTS simple_todo_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE simple_todo_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    priority ENUM('High', 'Medium', 'Low') NOT NULL,
    status ENUM('Pending', 'Completed') NOT NULL DEFAULT 'Pending',
    created_date DATE NOT NULL,
    estimated_date DATE NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role)
SELECT 'Admin', 'admin@gmail.com', '$2y$10$XcEVt4HD7OeirjAX.oUk3ehUomh11oFaHlJQJovsouhOaErHkN2uK', 'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@gmail.com'
);
