CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Example User (Never store passwords in plain text! Use password_hash)
INSERT INTO users (username, password) VALUES ('admin', '0000');  -- Replace with an actual hashed password