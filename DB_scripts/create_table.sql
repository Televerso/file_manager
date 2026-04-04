CREATE TABLE users (
	userID int NOT NULL,
	user_name varchar(64) NOT NULL,
    passhash varchar(64) NOT NULL,
    salt varchar(64) NOT NULL,
    PRIMARY KEY (userID)
);

CREATE INDEX idx_user_id_name ON users(userID, user_name);

CREATE TABLE files (
    fileID int NOT NULL,
    file_true_name varchar(128) NOT NULL,
    file_name varchar(64) NOT NULL,
    file_path varchar(255) NOT NULL,
    user_name varchar(64),
    userID int,
    time_modify DATETIME,

    PRIMARY KEY(fileID),
    CONSTRAINT user_id_name FOREIGN KEY (userID, user_name)
                            REFERENCES users(userID, user_name)
                            ON DELETE SET NULL
) ENGINE=InnoDB;