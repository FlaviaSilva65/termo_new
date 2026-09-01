-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.0.31 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para bd_termo
CREATE DATABASE IF NOT EXISTS `bd_termo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `bd_termo`;

-- Copiando estrutura para tabela bd_termo.escolas
CREATE TABLE IF NOT EXISTS `escolas` (
  `cd_unidade` int NOT NULL,
  `nm_unidade` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `ic_infantil` int NOT NULL DEFAULT (0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.escolas: ~39 rows (aproximadamente)
INSERT INTO `escolas` (`cd_unidade`, `nm_unidade`, `ic_infantil`) VALUES
	(2, 'E.M. Estina Campi Baptista\r\n5', 0),
	(6, 'E.M. Isabel Figuer“a Br‚fere, Prof¦\r\n7', 0),
	(8, 'E.M. SÆo Francisco de Assis\r\n9', 0),
	(10, 'E.M. Ana Maria Babette Bajer Fernandes\r\n11', 0),
	(12, 'E.M. Lions Clube Ocian\r\n13', 0),
	(14, 'E.M. Domingos Soares de Oliveira\r\n15', 0),
	(16, 'E.M. Elza Oliveira de Carvalho, Prof¦\r\n17', 0),
	(18, 'E.M. M rio Covas, Gov.\r\n19', 0),
	(20, 'E.M. Ary Cabral\r\n21', 0),
	(22, 'E.M. Maria Clotilde Lopes Comitre Rigo, Prof¦\r\n23', 0),
	(24, 'E.M. Thereza Magri\r\n25', 0),
	(26, 'E.M. Roberto Shoji, Dr.\r\n27', 0),
	(28, 'E.M. Joaquim Augusto Ferreira MourÆo\r\n29', 0),
	(30, 'E.M. Ronaldo S‚rgio A. Lameira Ramos\r\n31', 0),
	(32, 'E.M. Rep£blica de Portugal\r\n33', 0),
	(34, 'E.M. Carlos Eduardo Conte de Castro\r\n35', 0),
	(36, 'E.M. Eduardo Gonsalves do Barreiro\r\n37', 0),
	(38, 'E.M. Estado do Amazonas\r\n39', 0),
	(40, 'E.M. Hilda de Carvalho Guedes\r\n41', 0),
	(42, 'E.M. Id¡lio Perticaratti\r\n43', 0),
	(44, 'E.M. Jos‚ Ribeiro dos Santos Cunha\r\n45', 0),
	(46, 'E.M. Leopoldo Est sio Vanderlinde\r\n47', 0),
	(48, 'E.M. Maestro Luiz Arruda Paes\r\n49', 0),
	(50, 'E.M. Natale de Lucca \r\n51', 0),
	(52, 'E.M. Nicolau Paal\r\n53', 0),
	(54, 'E.M. Oscar Niemeyer, Arquiteto\r\n55', 0),
	(56, 'E.M. Paulo de Souza Sandoval\r\n57', 0),
	(63, 'E.M. S‚rgio Vieira de Mello \r\n64', 0),
	(65, 'E.M. Jos‚ Crego Painceira\r\n66', 0),
	(67, 'E.M. Manoel Nascimento Junior\r\n68', 0),
	(69, 'E.M. Maria de Lourdes Santos , Prof.¦\r\n70', 0),
	(71, 'E.M. Florivaldo Borges de Queiroz\r\n72', 0),
	(73, 'E.M. Orestes Qu‚rcia, Gov.\r\n74', 0),
	(75, 'E.M. Vereador Felipe Avelino Moraes\r\n77', 0),
	(78, 'E.M. S“nia Marise Domingues\r\n80', 0),
	(81, 'E.M. Prof§. Fued Temer\r\n84', 0),
	(85, 'E.M. Roberto Francisco dos Santos\r\n86', 0),
	(87, 'E.M. Visconde de Mau \r\n88', 0),
	(89, 'E.M. Isaura Campos Garcia\r\n90', 0);

-- Copiando estrutura para tabela bd_termo.setores
CREATE TABLE IF NOT EXISTS `setores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nm_setor` char(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.setores: ~8 rows (aproximadamente)
INSERT INTO `setores` (`id`, `nm_setor`, `created`, `modified`) VALUES
	(1, 'Setor 1', '2025-03-17 13:49:53', '2025-03-17 13:49:53'),
	(2, 'Setor 2', '2025-03-17 13:53:55', '2025-03-17 13:53:55'),
	(3, 'Setor 3', '2025-03-17 13:54:03', '2025-03-17 13:54:03'),
	(4, 'Setor 4', '2025-03-17 13:54:22', '2025-03-17 13:54:22'),
	(5, 'Setor 5', '2025-03-17 13:54:29', '2025-03-17 13:54:29'),
	(6, 'Setor 6', '2025-03-17 13:54:36', '2025-03-17 13:54:36'),
	(7, 'Setor 7', '2025-03-17 13:54:45', '2025-03-17 13:54:45'),
	(8, 'Setor 8', '2025-03-17 13:54:53', '2025-03-17 13:54:53');

-- Copiando estrutura para tabela bd_termo.setor_supervisores
CREATE TABLE IF NOT EXISTS `setor_supervisores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setores_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.setor_supervisores: ~4 rows (aproximadamente)
INSERT INTO `setor_supervisores` (`id`, `setores_id`, `usuario_id`, `created`, `modified`) VALUES
	(1, 1, 2, '2025-03-17 15:18:21', '2025-03-17 15:18:23'),
	(2, 2, 7, '2025-03-18 19:48:53', '2025-03-18 19:48:53'),
	(3, 2, 8, '2025-03-18 19:51:01', '2025-03-18 19:51:01'),
	(4, 2, 9, '2025-03-18 19:53:22', '2025-03-18 19:53:22');

-- Copiando estrutura para tabela bd_termo.setor_unid_escolares
CREATE TABLE IF NOT EXISTS `setor_unid_escolares` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setores_id` int NOT NULL,
  `unid_escolares_id` int NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.setor_unid_escolares: ~4 rows (aproximadamente)
INSERT INTO `setor_unid_escolares` (`id`, `setores_id`, `unid_escolares_id`, `created`, `modified`) VALUES
	(2, 1, 3, '2025-03-17 14:01:56', '2025-03-17 14:01:56'),
	(3, 1, 4, '2025-03-17 14:02:17', '2025-03-17 14:02:17'),
	(4, 1, 8, '2025-03-17 14:02:29', '2025-03-17 14:02:29'),
	(5, 2, 12, '2025-03-17 14:17:53', '2025-03-17 14:17:53');

-- Copiando estrutura para tabela bd_termo.tp_usuarios
CREATE TABLE IF NOT EXISTS `tp_usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nm_tp_usuarios` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.tp_usuarios: ~4 rows (aproximadamente)
INSERT INTO `tp_usuarios` (`id`, `nm_tp_usuarios`, `created`, `modified`) VALUES
	(1, 'Administrador', '2025-02-10 16:03:08', '2025-02-10 16:03:09'),
	(2, 'Supervisor', '2025-02-10 16:03:22', '2025-02-10 16:03:24'),
	(3, 'Diretor', '2025-02-10 16:03:33', '2025-02-10 16:03:34'),
	(4, 'Assistente Dir', '2025-02-10 16:03:59', '2025-02-10 16:04:00'),
	(5, 'Desenvolvedor', '2025-02-10 16:04:16', '2025-02-10 16:04:17');

-- Copiando estrutura para tabela bd_termo.unid_escolares
CREATE TABLE IF NOT EXISTS `unid_escolares` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nm_unid_escolar` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  `setores_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.unid_escolares: ~78 rows (aproximadamente)
INSERT INTO `unid_escolares` (`id`, `nm_unid_escolar`, `created`, `modified`, `setores_id`) VALUES
	(1, 'E.M. 19 de Janeiro (Complementação)', '2025-02-04 16:32:54', '2025-03-17 15:33:51', 8),
	(2, 'E.M. Albert Einstein', '2025-02-04 16:34:21', '2025-03-17 16:59:23', 6),
	(3, 'E.M. Anahy Navarro Trovão', '2025-02-04 16:34:48', '2025-03-17 15:34:15', 1),
	(4, 'E.M. Antônio Peres Ferreira ', '2025-02-04 16:35:16', '2025-03-17 15:34:28', 1),
	(5, 'E.M. Antonio Rubens Costa de Lara', '2025-02-04 16:35:44', '2025-02-04 16:35:45', 0),
	(6, 'E.M. Arquiteto Oscar Niemayer', '2025-02-04 16:36:20', '2025-02-04 16:36:21', 0),
	(7, 'E.M. Ary Cabral', '2025-02-04 16:36:57', '2025-02-04 16:36:58', 0),
	(8, 'E.M. Carlos Roberto Dias', '2025-02-04 16:37:09', '2025-03-17 18:58:52', 1),
	(9, 'E.M. Cidade da Criança', '2025-02-04 16:37:57', '2025-02-04 16:37:58', 0),
	(10, 'E.M. Circe Sanches Toschi', '2025-02-04 16:38:53', '2025-02-04 16:38:54', 0),
	(11, 'E.M. Domingos Soares de Oliveira', '2025-02-04 16:39:46', '2025-02-04 16:39:48', 0),
	(12, 'E.M. Dorivaldo F. Loria ', '2025-02-04 16:40:27', '2025-03-17 15:34:41', 2),
	(13, 'E.M. Dr. Roberto Shoji', '2025-02-04 16:41:05', '2025-02-04 16:41:07', 0),
	(14, 'E.M. Carlos Educardo Conte de Castro (Complementação)', '2025-02-04 16:42:08', '2025-02-04 16:42:09', 0),
	(15, 'E.M. Dr. Wilson Guedes', '2025-02-05 11:03:09', '2025-02-05 11:03:10', 0),
	(16, 'E.M. Drª Ana Maria B. B. Fernandes', '2025-02-05 11:04:36', '2025-02-05 11:04:38', 0),
	(17, 'E.M. Eduardo Gonsalves do Barreiro (Complementação)', '2025-02-05 11:05:35', '2025-02-05 11:05:37', 0),
	(18, 'E.M. Engº. Sérgio Dias de Freitas', '2025-02-05 11:08:34', '2025-02-05 11:08:35', 0),
	(19, 'E.M. Estado do Amazonas ', '2025-02-05 11:09:59', '2025-02-05 11:10:01', 0),
	(20, 'E.M. Estina Campi Baptista', '2025-02-05 11:11:13', '2025-03-17 18:59:04', 1),
	(21, 'E.M. Fausto dos Santos Amaral (Complementação)', '2025-02-05 11:14:10', '2025-02-05 11:14:12', 0),
	(22, 'E.M. Florivaldo Borges de Queiroz', '2025-02-05 11:14:35', '2025-02-05 11:14:38', 0),
	(23, 'E.M. Governador Franco Montoro', '2025-02-05 11:15:49', '2025-02-05 11:15:51', 0),
	(24, 'E.M. Governador Mário Covas', '2025-02-05 11:16:26', '2025-02-05 11:16:28', 0),
	(25, 'E.M. Governador Orestes Quércia', '2025-02-05 11:17:08', '2025-02-05 11:17:09', 0),
	(26, 'E.M. Gregório França de Siqueira', '2025-02-05 11:18:05', '2025-02-05 11:18:07', 0),
	(27, 'E.M. Hilda Guedes (Complementação)', '2025-02-05 11:19:08', '2025-02-05 11:19:09', 0),
	(28, 'E.M. Icaro Batista Cardoso', '2025-02-05 11:22:25', '2025-03-17 18:59:21', 1),
	(29, 'E.M. Idalina da Conceição Pereira', '2025-02-05 11:23:29', '2025-02-05 11:23:30', 0),
	(30, 'E.M. Idílio Perticaratti', '2025-02-05 11:24:27', '2025-02-05 11:24:29', 0),
	(31, 'E.M. João Batista Resine Alves', '2025-02-05 11:25:16', '2025-02-05 11:25:17', 0),
	(32, 'E.M. João Gonçalves', '2025-02-05 11:26:08', '2025-02-05 11:26:09', 0),
	(33, 'E.M. Joaquim Augusto F. Mourão', '2025-02-05 11:27:07', '2025-02-05 11:27:09', 0),
	(34, 'E.M. José Crego Painceira', '2025-02-05 11:28:02', '2025-02-05 11:28:03', 0),
	(35, 'E.M. José Júlio Martins Baptista', '2025-02-05 11:28:49', '2025-02-05 11:28:50', 0),
	(36, 'E.M. José Padin Mouta', '2025-02-05 11:30:45', '2025-02-05 11:30:46', 0),
	(37, 'E.M. José Ribeiro dos Santos Cunha', '2025-02-05 11:31:29', '2025-02-05 11:31:30', 0),
	(38, 'E.M. Juliana Arias R. de Oliveira', '2025-02-05 11:32:37', '2025-02-05 11:32:38', 0),
	(39, 'E.M. Layde R. Reis Loria', '2025-02-05 11:33:18', '2025-02-05 11:33:19', 0),
	(40, 'E.M. Leopoldo Estasio Vanderlinde', '2025-02-05 11:34:45', '2025-02-05 11:34:46', 0),
	(41, 'E.M. Lions Clube Ocian', '2025-02-05 11:36:33', '2025-02-05 11:36:34', 0),
	(42, 'E.M. Luzia Borba Ranciaro', '2025-02-05 11:37:18', '2025-02-05 11:37:19', 0),
	(43, 'E.M. Maestro Luis Arruda Paes', '2025-02-05 11:38:27', '2025-02-05 11:38:28', 0),
	(44, 'E.M. Mahatma Gandhi', '2025-02-05 11:39:09', '2025-02-05 11:39:10', 0),
	(45, 'E.M. Manoel Nascimento Junior', '2025-02-05 11:40:01', '2025-03-17 18:59:36', 1),
	(46, 'E.M. Maria dos Remédios C. Milan', '2025-02-05 11:41:01', '2025-02-05 11:41:02', 0),
	(47, 'E.M. Mário Possani', '2025-02-05 11:42:42', '2025-02-05 11:42:44', 0),
	(48, 'E.M. Natale de Lucca', '2025-02-05 11:43:27', '2025-02-05 11:43:28', 0),
	(49, 'E.M. Newton de Almeida Castro', '2025-02-05 11:44:27', '2025-02-05 11:44:28', 0),
	(50, 'E.M. Nicolau Paal', '2025-02-05 11:45:12', '2025-02-05 11:45:14', 0),
	(51, 'E.M. Ophélia Caccetari dos Reis', '2025-02-05 11:45:54', '2025-02-05 11:45:57', 0),
	(52, 'E.M. Oswaldo Justo', '2025-02-05 11:46:37', '2025-02-05 11:46:38', 0),
	(53, 'E.M. Pablo Trevisan Perutich', '2025-02-05 11:47:08', '2025-02-05 11:47:09', 0),
	(54, 'E.M. Paulo de Souza Sandoval ', '2025-02-05 11:47:40', '2025-02-05 11:47:41', 0),
	(55, 'E.M. Paulo Shigueo Yamauti', '2025-02-05 11:48:20', '2025-03-17 18:59:49', 1),
	(56, 'E.M. Profº. Fued Miguel Temer', '2025-02-05 11:48:53', '2025-02-05 11:48:54', 0),
	(57, 'E.M. Prof.ª Elza Oliveira de Carvalho', '2025-02-05 11:49:22', '2025-02-05 11:49:23', 0),
	(58, 'E.M. Prof.ª Isabel Figueroa Bréfere ', '2025-02-05 11:50:03', '2025-02-05 11:50:04', 0),
	(59, 'E.M. Profª Esmeralda dos S. Novaes', '2025-02-05 11:51:07', '2025-02-05 11:51:08', 0),
	(60, 'E.M. Profª Mª Nilza da Silva Romão', '2025-02-05 11:52:20', '2025-02-05 11:52:21', 0),
	(61, 'E.M. Profª Maria Clotilde L. C. Rigo', '2025-02-05 11:53:06', '2025-02-05 11:53:07', 0),
	(62, 'E.M. Profª Maria de Lourdes Santos', '2025-02-05 11:54:12', '2025-02-05 11:54:13', 0),
	(63, 'E.M. República de Portugal', '2025-02-05 11:55:07', '2025-02-05 11:55:08', 0),
	(64, 'E.M. Roberto Francisco dos Santos', '2025-02-05 11:55:47', '2025-02-05 11:55:49', 0),
	(65, 'E.M. Roberto Mário Santini', '2025-02-05 11:56:36', '2025-02-05 11:56:37', 0),
	(66, 'E.M. Ronaldo Sérgio A. L. Ramos', '2025-02-05 11:57:23', '2025-02-05 11:57:24', 0),
	(67, 'E.M. Ruth Vilaça Correia L. Cardoso', '2025-02-05 11:58:06', '2025-02-05 11:58:08', 0),
	(68, 'E.M. São Francisco de Assis', '2025-02-05 11:58:52', '2025-03-17 19:00:02', 1),
	(69, 'E.M. Sebastião Tavares de Oliveira', '2025-02-05 11:59:33', '2025-02-05 11:59:34', 0),
	(70, 'E.M. Sérgio Vieira de Mello', '2025-02-05 12:00:12', '2025-02-05 12:00:13', 0),
	(71, 'E.M. Sonia Marise (Complementação)', '2025-02-05 12:01:31', '2025-02-05 12:01:32', 0),
	(72, 'E.M. Thereza Magri', '2025-02-05 12:02:20', '2025-02-05 12:02:22', 0),
	(73, 'E.M. Valter Salerno', '2025-02-05 12:02:50', '2025-02-05 12:02:51', 0),
	(74, 'E.M. Vereador Felipe Avelino Moraes', '2025-02-05 12:13:03', '2025-02-05 12:13:04', 0),
	(75, 'E.M. Vereadora Isaura Campos Garcia', '2025-02-05 12:13:55', '2025-02-05 12:13:56', 0),
	(76, 'E.M. Vila Mirim', '2025-02-05 12:14:35', '2025-03-17 19:00:14', 1),
	(77, 'E.M. Vila Tupiry', '2025-02-05 12:15:14', '2025-02-05 12:15:15', 0),
	(78, 'E.M. Visconde de Mauá', '2025-02-05 12:16:08', '2025-02-05 12:16:10', 0);

-- Copiando estrutura para tabela bd_termo.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cd_rf` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nm_usuario` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tp_usuarios_id` int NOT NULL,
  `cd_assinatura` char(10) COLLATE utf8mb4_general_ci NOT NULL,
  `ic_ativo` int DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.usuarios: ~12 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `cd_rf`, `nm_usuario`, `email`, `username`, `password`, `tp_usuarios_id`, `cd_assinatura`, `ic_ativo`, `created`, `modified`) VALUES
	(1, '47093', 'Flavia Silva', 'flavia@educacaopg.sp.gov.br', 'flaviasilva', '$2y$10$Di3fGJ4qYpYahGWjNt/LQecuq3IJ5ZyaTVAnAP8xA0xfctoDKfNFW', 5, '', 0, '2025-02-11 12:03:50', '2025-02-11 12:03:50'),
	(2, '12153', 'Maria Martins Leite', 'mariam@educacaopg.sp.gov.br', 'mariamartins', '$2y$10$0bKJv8bjwDHcdipIpLJleOPQlas9k/FRdrK8vGR0YziO/CWeRDNX.', 2, '', 0, '2025-02-11 13:36:24', '2025-03-10 19:52:52'),
	(3, '99999', 'Paula Elias Silva', 'paulaesilva@gmail.com', 'paulaes', '$2y$10$irV3GPgQO0yxTjpzKH1rnu/3LLGs/kClk6sShQHbbO.npt8pFtK6K', 4, '', 0, '2025-03-10 18:29:29', '2025-03-10 18:29:29'),
	(4, '99998', 'Teste Teste', 'teste@gmail.com', 'testeteste', '$2y$10$eza4uFlWyXQQoZreaHJPuOmt4QxZbxk6BoQddgMsB11ywYZp2j49W', 4, '99998.jpeg', 0, '2025-03-12 18:56:32', '2025-03-12 18:56:32'),
	(5, '16936', 'Arminda Mendes Cecchi', 'teste@gmail.com', 'armindacecchi', '$2y$10$v4Q81EiQ5NgpY31ErwLjUONch6Sg/cfAiHyyNUjzrWllDeBKqCVJS', 2, '16936.jpg', 0, '2025-03-13 18:25:51', '2025-03-13 18:25:51'),
	(6, '27083', 'Rita de Cássia Andrade', 'teste@gmail.com', 'ritacandrade', '$2y$10$3BgpkGsXTBMmSbZgJqyRued3dTy93XYpwVFbQWzBW8BuJcInYYCu2', 2, '27083.jpeg', 0, '2025-03-14 15:14:21', '2025-03-14 15:14:21'),
	(8, '98996', 'Pedro Teste', 'pedroteste@gmail.com', 'pedrot123', '$2y$10$iK7t/YxvRoa02arhXSU/aegRsINK4PRy71doKkYa0m4tmJ6JqNYGC', 2, '98996.jpg', 0, '2025-03-18 19:51:00', '2025-03-18 19:51:00'),
	(9, '98995', 'Pedro Teste 2', 'pedroteste2@gmail.com', 'pedrot1234', '$2y$10$uZnHf.dGdFvxCB5XJDtq2.5jG5SJM5vJTwsA18nIHf3twn4/hI1WC', 2, '98995.jpg', 1, '2025-03-18 19:53:22', '2025-03-18 19:55:56'),
	(10, '98996', 'Pedro Teste 3', 'pedroteste3@gmail.com', 'pedrot12345', '$2y$10$YjxRdITzqSXewmp58JBXAuMqvB0Z81O2w6JUx3yyzkI0Px/mNiXnm', 1, '98996.jpg', 0, '2025-03-20 13:48:03', '2025-03-20 13:48:03'),
	(11, '98994', 'Pedro Teste 4', 'flavia@educacaopg.sp.gov.br', 'pedroteste4', '$2y$10$8/388ta8sz2KwnrileE5u.kVWv0y6V2BpPIuNkdULycnB9QTrLAoa', 4, '98994.jpg', 0, '2025-03-20 14:46:06', '2025-03-20 14:46:06'),
	(12, '98994', 'Pedro Teste 4', 'flavia@educacaopg.sp.gov.br', 'pedroteste5', '$2y$10$o.OiDz./liJAx/U44rwoA.LUhYpxyty60vFTcIoJhpbnupTW57fxu', 4, '98994.jpg', 0, '2025-03-20 14:54:41', '2025-03-20 14:54:41'),
	(13, '16934', 'Arminda Mendes Cecchi', 'teste@gmail.com', 'flaviasilvarr', '$2y$10$lB4Il3bAlugnc1PTxNOUve4PfG9nfWi0GNIFTxlprh3vsV3wpJIne', 5, '16934.jpg', 0, '2025-03-21 13:35:03', '2025-03-21 13:35:03');

-- Copiando estrutura para tabela bd_termo.usuario_unid_escolares
CREATE TABLE IF NOT EXISTS `usuario_unid_escolares` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `unid_escolares_id` int NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela bd_termo.usuario_unid_escolares: ~5 rows (aproximadamente)
INSERT INTO `usuario_unid_escolares` (`id`, `usuario_id`, `unid_escolares_id`, `created`, `modified`) VALUES
	(1, 6, 10, '2025-03-14 15:14:21', '2025-03-14 15:14:21'),
	(3, 5, 12, '2025-03-14 15:35:52', '2025-03-14 15:35:52'),
	(4, 2, 3, '2025-03-14 15:36:59', '2025-03-14 15:36:59'),
	(5, 9, 1, '2025-03-18 19:55:56', '2025-03-18 19:55:56'),
	(6, 12, 12, '2025-03-20 14:54:41', '2025-03-20 14:54:41');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
