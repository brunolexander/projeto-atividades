CREATE DATABASE IF NOT EXISTS `projeto-atividades` DEFAULT CHARACTER SET=`utf8mb4` COLLATE `utf8mb4_unicode_520_ci`;
USE `projeto-atividades`;

CREATE TABLE IF NOT EXISTS `usuarios` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(255),
	`email` VARCHAR(255) NOT NULL,
	`senha` VARCHAR(255) NOT NULL,
	`criado_em` INT(11) NOT NULL,
	`acesso_em` INT(11),
	`permissao` INT(11) DEFAULT 1,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `categorias` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(100),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `atividades` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`titulo` VARCHAR(255) NOT NULL,
	`descricao` TEXT,
	`criado_em` INT(11) NOT NULL,
	`atualizado_em` INT(11) DEFAULT NULL,
	`expira_em` INT(11) NOT NULL,
	`categoria` INT(11) NOT NULL,
	`autor` INT(11) NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY (`categoria`) REFERENCES `categorias`(`id`),
	FOREIGN KEY (`autor`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;