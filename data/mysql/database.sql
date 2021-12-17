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

CREATE TABLE IF NOT EXISTS `permissoes` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nome` VARCHAR(64) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `permissoes_usuario` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`usuario_id` INT(11) NOT NULL,
	`permissao_id` INT(11) NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
	FOREIGN KEY (`permissao_id`) REFERENCES `permissoes` (`id`),
	UNIQUE (`usuario_id`, `permissao_id`)
);

INSERT INTO `usuarios` (`id`, `email`, `senha`, `criado_em`) VALUES (1, 'admin@admin.com', '$2y$10$gnmhc423zva8TPto9fx9lu7cz5M41Oazu7x9taGVp19otWeMUy2Um', UNIX_TIMESTAMP());

INSERT INTO `categorias` (`nome`) VALUES ('Desenvolvimento'), ('Atendimento'), ('Manutenção'), ('Manutenção urgente');

INSERT INTO `permissoes` VALUES (1, 'ver_usuários');
INSERT INTO `permissoes` VALUES (2, 'criar_usuários');
INSERT INTO `permissoes` VALUES (3, 'editar_usuários');
INSERT INTO `permissoes` VALUES (4, 'remover_usuários');
INSERT INTO `permissoes` VALUES (5, 'ver_atividades');
INSERT INTO `permissoes` VALUES (6, 'criar_atividades');
INSERT INTO `permissoes` VALUES (7, 'editar_atividades');
INSERT INTO `permissoes` VALUES (8, 'remover_atividades');
INSERT INTO `permissoes` VALUES (9, 'finalizar_atividades');
INSERT INTO `permissoes` VALUES (10, 'adicionar_categorias');
INSERT INTO `permissoes` VALUES (11, 'remover_categorias');

INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 1);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 2);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 3);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 4);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 5);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 6);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 7);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 8);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 9);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 10);
INSERT INTO `permissoes_usuario` (`usuario_id`, `permissao_id`) VALUES (1, 11);