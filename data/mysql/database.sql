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
	`session_hash` VARCHAR(255),
	PRIMARY KEY(`id`),
	UNIQUE(`email`)
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
	`finalizada` INT(11) DEFAULT 0,
	`categoria` INT(11),
	`autor` INT(11) NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY (`categoria`) REFERENCES `categorias`(`id`) ON DELETE SET NULL,
	FOREIGN KEY (`autor`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB;


INSERT INTO `usuarios` (`email`, `senha`, `criado_em`) VALUES ('admin@admin.com', '$2y$10$gnmhc423zva8TPto9fx9lu7cz5M41Oazu7x9taGVp19otWeMUy2Um', UNIX_TIMESTAMP());

INSERT INTO `categorias` (`nome`) VALUES ('Desenvolvimento'), ('Atendimento'), ('Manutenção'), ('Manutenção urgente');