CREATE TABLE IF NOT EXISTS users 
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

/* 
trzeba zarejestrować jakiegoś użytkownika, baza jest pusta
*/