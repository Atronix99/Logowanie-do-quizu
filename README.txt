tworzenie bazy dla userów.

http://localhost/Logowanie/index.php

CREATE DATABASE loginy;
CREATE TABLE uzytkownicy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    dog_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL,
    token_logowania VARCHAR(255) DEFAULT NULL,
    data_rejestracji TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    punkty_quizu INT DEFAULT 0,
    max_punkty INT DEFAULT 0
);