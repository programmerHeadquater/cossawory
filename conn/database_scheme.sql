-- Create a new database
CREATE DATABASE IF NOT EXISTS cassowary_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- Use the new database
USE cassowary_db;



CREATE TABLE IF NOT EXISTS submission (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_data JSON NOT NULL,
  review BOOLEAN DEFAULT FALSE,
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;






CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255), -- username should be unique
  email VARCHAR(255) UNIQUE, -- email address of the user 
  password VARCHAR(255), -- password will be hashed by php hash function
  view BOOLEAN DEFAULT TRUE, -- check if user allow to see 
  can_write_review BOOLEAN DEFAULT FALSE, -- check if user allow to write a review
  can_delete_review BOOLEAN DEFAULT FALSE, -- check if user allow to delete the review
  can_delete_submission BOOLEAN DEFAULT FALSE, -- check if user allow to delete the whole querry
  can_add_user BOOLEAN DEFAULT FALSE, -- check if user allow to add user who can review to dashboard (login creation for other)
  can_delete_user BOOLEAN DEFAULT FALSE, -- check if user can delete the other user in the dashboard 
  can_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- testing tables

