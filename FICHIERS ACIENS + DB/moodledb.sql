-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/04/2025 às 23:15
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `moodledb`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250408093557', '2025-04-08 11:36:24', 55),
('DoctrineMigrations\\Version20250408093928', '2025-04-08 11:39:39', 17),
('DoctrineMigrations\\Version20250408094340', '2025-04-08 11:43:45', 89),
('DoctrineMigrations\\Version20250408095118', '2025-04-08 11:51:25', 88),
('DoctrineMigrations\\Version20250408095410', '2025-04-08 11:54:19', 49);

-- --------------------------------------------------------

--
-- Estrutura para tabela `file`
--

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `file`
--

INSERT INTO `file` (`id`, `post_id`, `file_path`) VALUES
(1, 1, 'path/to/file1.jpg'),
(2, 4, 'path/to/file4.jpg'),
(3, 6, 'path/to/file6.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` int(11) NOT NULL,
  `user_id_id` int(11) NOT NULL,
  `ue_id_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `user_id_id`, `ue_id_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 4),
(5, 2, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id_id` int(11) NOT NULL,
  `ue_id_id` int(11) NOT NULL,
  `message` longtext DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `post`
--

INSERT INTO `post` (`id`, `user_id_id`, `ue_id_id`, `message`, `date`) VALUES
(1, 1, 1, 'First message for ue_id 1', '2025-04-16 00:00:00'),
(2, 1, 1, 'Second message for ue_id 1', '2025-04-16 00:00:00'),
(3, 1, 4, 'First message for ue_id 4', '2025-04-16 00:00:00'),
(4, 1, 4, 'Second message for ue_id 4', '2025-04-16 00:00:00'),
(5, 1, 4, 'Third message for ue_id 4', '2025-04-16 00:00:00'),
(6, 1, 4, 'Fourth message for ue_id 4', '2025-04-16 00:00:00'),
(7, 1, 2, 'this is a new post', '2025-04-19 21:30:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ue`
--

CREATE TABLE `ue` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `ue`
--

INSERT INTO `ue` (`id`, `code`, `title`, `image_path`) VALUES
(1, 'WE41', 'Thermodynamics and Heat Transfer', 'WE41.jpg'),
(2, 'IT41', 'Object-Oriented Programming', 'IT41.jpg'),
(3, 'IT86', 'Embedded Systems Design', 'IT86.jpg'),
(4, 'WE41', 'Fluid Mechanics', 'WE41.jpg'),
(5, 'IT86', 'Digital Signal Processing', 'IT86.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`, `surname`) VALUES
(1, 'patrick@utbm.fr', '[]', '$2y$13$DlEGoIEIRCQ9eCwpDX3cSeVXft9l65FJpXC7TiZ8InMO5EY7m02VK', 'Patrick', 'Aguiar'),
(2, 'florent@utbm.fr', '[]', '$2y$13$9TgQmwh.C3WAH0TXDibO2uuCevMl4q2yS9xq.4jVVliOive9XYbDi', 'florent', 'vosin'),
(3, 'teste1@utbm.fr', '[\"ROLE_STUDENT\"]', 'teste124', 'teste', 'ttt'),
(4, 'patricolino@gmail.com', '[\"ROLE_TEACHER\"]', 'teste124', 'form', 'ttt'),
(5, 'patricolino2@gmail.com', '[\"ROLE_STUDENT\"]', 'teste124', 'form', 'ttt');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Índices de tabela `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8C9F36104B89032C` (`post_id`);

--
-- Índices de tabela `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_74E0281C9D86650F` (`user_id_id`),
  ADD KEY `IDX_74E0281C1CA2F0B7` (`ue_id_id`);

--
-- Índices de tabela `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Índices de tabela `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A8A6C8D9D86650F` (`user_id_id`),
  ADD KEY `IDX_5A8A6C8D1CA2F0B7` (`ue_id_id`);

--
-- Índices de tabela `ue`
--
ALTER TABLE `ue`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `file`
--
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `ue`
--
ALTER TABLE `ue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `FK_8C9F36104B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
-- Restrições para tabelas `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `FK_74E0281C1CA2F0B7` FOREIGN KEY (`ue_id_id`) REFERENCES `ue` (`id`),
  ADD CONSTRAINT `FK_74E0281C9D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `user` (`id`);

--
-- Restrições para tabelas `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8D1CA2F0B7` FOREIGN KEY (`ue_id_id`) REFERENCES `ue` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8D9D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
