CREATE TABLE IF NOT EXISTS users 
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    /* tokeny */
    refresh_token VARCHAR(512) NULL,
    refresh_token_expires_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS tasks
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority INT NOT NULL DEFAULT 1,
    due_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) 
        REFERENCES users(id) 
        ON DELETE CASCADE
);

/* 
trzeba zarejestrować jakiegoś użytkownika, baza jest pusta
*/