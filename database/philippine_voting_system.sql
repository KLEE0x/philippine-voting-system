-- Philippine Voting Management System
-- Import this file in phpMyAdmin.
-- Default database name: philippine_voting_system

DROP DATABASE IF EXISTS philippine_voting_system;
CREATE DATABASE philippine_voting_system CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE philippine_voting_system;

CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_roles FOREIGN KEY (role_id) REFERENCES roles(role_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE precincts (
    precinct_id INT AUTO_INCREMENT PRIMARY KEY,
    precinct_number VARCHAR(50) NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    UNIQUE KEY unique_precinct (precinct_number, barangay, city)
) ENGINE=InnoDB;

CREATE TABLE voters (
    voter_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    precinct_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birthdate DATE NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    address VARCHAR(255) NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    registration_status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_voters_users FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_voters_precincts FOREIGN KEY (precinct_id) REFERENCES precincts(precinct_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE elections (
    election_id INT AUTO_INCREMENT PRIMARY KEY,
    election_name VARCHAR(150) NOT NULL,
    election_type VARCHAR(80) NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    status ENUM('upcoming','open','closed') NOT NULL DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE positions (
    position_id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    position_name VARCHAR(100) NOT NULL,
    max_vote INT NOT NULL DEFAULT 1,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    CONSTRAINT fk_positions_elections FOREIGN KEY (election_id) REFERENCES elections(election_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY unique_position_per_election (election_id, position_name)
) ENGINE=InnoDB;

CREATE TABLE candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    position_id INT NOT NULL,
    candidate_name VARCHAR(150) NOT NULL,
    party_name VARCHAR(120) DEFAULT 'Independent',
    platform TEXT,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_candidates_elections FOREIGN KEY (election_id) REFERENCES elections(election_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_candidates_positions FOREIGN KEY (position_id) REFERENCES positions(position_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_candidates_position (position_id)
) ENGINE=InnoDB;

CREATE TABLE votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    election_id INT NOT NULL,
    voter_id INT NOT NULL,
    candidate_id INT NOT NULL,
    position_id INT NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_votes_elections FOREIGN KEY (election_id) REFERENCES elections(election_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_votes_voters FOREIGN KEY (voter_id) REFERENCES voters(voter_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_votes_candidates FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_votes_positions FOREIGN KEY (position_id) REFERENCES positions(position_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY unique_vote_candidate (election_id, voter_id, candidate_id),
    INDEX idx_votes_result (election_id, position_id, candidate_id)
) ENGINE=InnoDB;

CREATE TABLE voter_election_status (
    voter_election_status_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_id INT NOT NULL,
    election_id INT NOT NULL,
    has_voted TINYINT(1) NOT NULL DEFAULT 0,
    voted_at DATETIME NULL,
    CONSTRAINT fk_status_voters FOREIGN KEY (voter_id) REFERENCES voters(voter_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_status_elections FOREIGN KEY (election_id) REFERENCES elections(election_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY unique_voter_election (voter_id, election_id)
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_users FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_logs_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE system_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    setting_name VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT
) ENGINE=InnoDB;

INSERT INTO roles (role_id, role_name) VALUES
(1, 'admin'),
(2, 'election_officer'),
(3, 'voter'),
(4, 'auditor');

INSERT INTO users (user_id, username, password, role_id, status) VALUES
(1, 'admin', '$2y$12$sFmHI1rQhM3mO4NwtxWIw.oDrvrRMoumfOCjxckxOuZezQF6Bpza6', 1, 'active'),
(2, 'officer', '$2y$12$gZw3R7Se12.6rnwHpmD5YuhrOhSnJdEreHNuItLdeRVOmT6Wk0jzK', 2, 'active'),
(3, 'auditor', '$2y$12$x7PNDfn8CVYcw7FLiNojxe7C4J7ru4JNn/CNCE1tdxCsx0Nf6IldS', 4, 'active'),
(4, 'voter1', '$2y$12$pOThhLXeOeJqxgBcp8EqcezxyusGB7q8CdSAgJXdUNI0xb/K/UYTi', 3, 'active'),
(5, 'voter2', '$2y$12$pOThhLXeOeJqxgBcp8EqcezxyusGB7q8CdSAgJXdUNI0xb/K/UYTi', 3, 'active'),
(6, 'voter3', '$2y$12$pOThhLXeOeJqxgBcp8EqcezxyusGB7q8CdSAgJXdUNI0xb/K/UYTi', 3, 'active'),
(7, 'voter4', '$2y$12$pOThhLXeOeJqxgBcp8EqcezxyusGB7q8CdSAgJXdUNI0xb/K/UYTi', 3, 'active'),
(8, 'voter5', '$2y$12$pOThhLXeOeJqxgBcp8EqcezxyusGB7q8CdSAgJXdUNI0xb/K/UYTi', 3, 'active');

INSERT INTO precincts (precinct_id, precinct_number, barangay, city) VALUES
(1, '001A', 'Barangay Bagong Pag-asa', 'Quezon City'),
(2, '002B', 'Barangay Commonwealth', 'Quezon City'),
(3, '003C', 'Barangay Holy Spirit', 'Quezon City');

INSERT INTO voters (voter_id, user_id, precinct_id, first_name, last_name, birthdate, gender, address, barangay, city, registration_status) VALUES
(1, 4, 1, 'Juan', 'Dela Cruz', '1998-03-12', 'Male', '123 Mabini Street', 'Barangay Bagong Pag-asa', 'Quezon City', 'approved'),
(2, 5, 1, 'Maria', 'Santos', '1999-07-20', 'Female', '45 Rizal Avenue', 'Barangay Bagong Pag-asa', 'Quezon City', 'approved'),
(3, 6, 2, 'Carlo', 'Reyes', '2000-01-15', 'Male', '78 Bonifacio Road', 'Barangay Commonwealth', 'Quezon City', 'approved'),
(4, 7, 2, 'Ana', 'Garcia', '1997-09-08', 'Female', '11 Luna Street', 'Barangay Commonwealth', 'Quezon City', 'pending'),
(5, 8, 3, 'Paolo', 'Ramos', '1996-11-30', 'Male', '22 Jacinto Street', 'Barangay Holy Spirit', 'Quezon City', 'approved');

INSERT INTO elections (election_id, election_name, election_type, start_datetime, end_datetime, status) VALUES
(1, '2026 Demo Philippine Local Election', 'Local Election', '2026-01-01 08:00:00', '2030-12-31 17:00:00', 'open');

INSERT INTO positions (position_id, election_id, position_name, max_vote, status) VALUES
(1, 1, 'Mayor', 1, 'active'),
(2, 1, 'Vice Mayor', 1, 'active'),
(3, 1, 'Councilor', 3, 'active'),
(4, 1, 'Barangay Captain', 1, 'active'),
(5, 1, 'SK Chairman', 1, 'active');

INSERT INTO candidates (candidate_id, election_id, position_id, candidate_name, party_name, platform, status) VALUES
(1, 1, 1, 'Jose Fernandez', 'Partido Pag-asa', 'Improved public services, traffic management, and local livelihood programs.', 'active'),
(2, 1, 1, 'Liza Mendoza', 'Bagong Bayan Party', 'Transparent governance, youth programs, and health services.', 'active'),
(3, 1, 2, 'Marco Villanueva', 'Partido Pag-asa', 'Disaster preparedness and community development.', 'active'),
(4, 1, 2, 'Grace Bautista', 'Independent', 'Clean leadership and faster barangay coordination.', 'active'),
(5, 1, 3, 'Allen Cruz', 'Partido Pag-asa', 'Education support and livelihood training.', 'active'),
(6, 1, 3, 'Bea Santiago', 'Bagong Bayan Party', 'Women empowerment and community health.', 'active'),
(7, 1, 3, 'Dennis Lim', 'Independent', 'Peace and order plus youth sports program.', 'active'),
(8, 1, 3, 'Nina Flores', 'Partido Pag-asa', 'Environmental projects and senior citizen support.', 'active'),
(9, 1, 4, 'Ramon Castillo', 'Independent', 'Barangay safety and clean surroundings.', 'active'),
(10, 1, 5, 'Trisha Aquino', 'Kabataan Movement', 'Youth leadership, sports, and scholarship assistance.', 'active');

INSERT INTO voter_election_status (voter_id, election_id, has_voted, voted_at) VALUES
(1, 1, 0, NULL),
(2, 1, 0, NULL),
(3, 1, 0, NULL),
(4, 1, 0, NULL),
(5, 1, 0, NULL);

INSERT INTO system_settings (setting_name, setting_value) VALUES
('system_name', 'Philippine Voting Management System'),
('school_project_note', 'This is a school project demo inspired by the Philippine voting process.');

INSERT INTO audit_logs (user_id, action, description) VALUES
(1, 'SYSTEM_SETUP', 'Initial sample database imported successfully.');
