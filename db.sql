CREATE DATABASE IF NOT EXISTS db_aula;
USE db_aula;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nome VARCHAR(50) DEFAULT NULL,
  email VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO usuarios (nome, email) VALUES ('Eduardo Lacerda', 'edulacerdaaa@gmail.com');
