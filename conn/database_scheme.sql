-- Create a new database
CREATE DATABASE IF NOT EXISTS Cossawory_db;
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- Use the new database
USE Cossawory_db;

-- Create a table called submission
CREATE TABLE IF NOT EXISTS submission (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  concern TEXT NOT NULL,
  disability VARCHAR(255),      -- Optional
  why_this_app TEXT,                 -- Optional
  review BOOLEAN,
  review_id Int,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
