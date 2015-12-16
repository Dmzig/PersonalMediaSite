DROP TABLE IF EXISTS users.log;
DROP TABLE IF EXISTS users.authentication;
DROP TABLE IF EXISTS users.user_info;

DROP SCHEMA IF EXISTS users;

CREATE SCHEMA users;

-- Table: users.user_info
-- Columns:
--    username          - The username for the account, supplied during registration.
--    registration_date - The date the user registered. Set automatically.
--    description       - A user-supplied description.
CREATE TABLE users.user_info (
	username 		VARCHAR(30) PRIMARY KEY,
	registration_date 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	description 		VARCHAR(500)
);

-- Table: users.authentication
-- Columns:
--    username      - The username tied to the authentication info.
--    password_hash - The hash of the user's password + salt. Expected to be SHA1.
--    salt          - The salt to use. Expected to be a SHA1 hash of a random input.
CREATE TABLE users.authentication (
	username 	VARCHAR(30) PRIMARY KEY,
	password_hash 	CHAR(40) NOT NULL,
	salt 		CHAR(40) NOT NULL,
	FOREIGN KEY (username) REFERENCES users.user_info(username)
);

-- Table: users.log
-- Columns:
--    log_id     - A unique ID for the log entry. Set by a sequence.
--    username   - The user whose action generated this log entry.
--    ip_address - The IP address of the user at the time the log was entered.
--    log_date   - The date of the log entry. Set automatically by a default value.
--    action     - What the user did to generate a log entry (i.e., "logged in").
CREATE TABLE users.log (
	log_id  	SERIAL PRIMARY KEY,
	username 	VARCHAR(30) NOT NULL REFERENCES users.user_info,
	ip_address 	VARCHAR(15) NOT NULL,
	log_date 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	action 		VARCHAR(50) NOT NULL
);

CREATE INDEX log_id_index ON users.log (username);