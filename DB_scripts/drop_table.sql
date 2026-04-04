ALTER TABLE files DROP FOREIGN KEY user_id_name;
DROP INDEX idx_user_id_name on users;
DROP INDEX user_id_name on files;

DROP TABLE files;
DROP TABLE users;
