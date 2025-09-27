-- Create a new database
CREATE DATABASE IF NOT EXISTS Cossawory_db;
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- Use the new database
USE Cossawory_db;

CREATE TABLE IF NOT EXISTS submission  (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,   -- tittle of problem
  concern TEXT NOT NULL,          -- description of problem 
  why_this_app TEXT,                 -- Optional
  review BOOLEAN,             -- this check if it is review or not 
  submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Create a table called submission
-- CREATE TABLE IF NOT EXISTS submission (
--   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--   title VARCHAR(255) NOT NULL,   --tittle of problem
--   concern TEXT NOT NULL,          --description of problem 
--   why_this_app TEXT,                 -- Optional
--   review BOOLEAN,             --this check if it is review or not 
--   review_id Int,              -- this will link to review table 
--   submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Create a table called submission
CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- primary key
    submission_id INT UNSIGNED NOT NULL, -- id from submission table to link 
    review TEXT NOT NULL, -- review add for submission_id on table submission 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- created time 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- this is updated time stamp on update
    FOREIGN KEY (submission_id) REFERENCES submission(id) ON DELETE CASCADE -- if submission table id delete, this delete all data linked on this table too
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255), -- username should be unique
  email VARCHAR(255) UNIQUE, -- email address of the user 
  password VARCHAR(255), -- password will be hashed by php hash function
  view BOOLEAN DEFAULT TRUE, -- check if user allow to see 
  can_write_review BOOLEAN DEFAULT FALSE, -- check if user allow to write a review
  can_delete_review BOOLEAN DEFAULT FALSE, -- check if user allow to delete the review
  can_delete_querry BOOLEAN DEFAULT FALSE, -- check if user allow to delete the whole querry
  can_add_user BOOLEAN DEFAULT FALSE, -- check if user allow to add user who can review to dashboard (login creation for other)
  can_delete_user BOOLEAN DEFAULT FALSE, -- check if user can delete the other user in the dashboard 
  can_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



