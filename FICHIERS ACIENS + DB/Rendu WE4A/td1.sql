-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/04/2025 às 23:53
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
-- Banco de dados: `td1`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `devoir`
--

CREATE TABLE `devoir` (
  `id_devoir` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `consigne` text DEFAULT NULL,
  `date_limite` date DEFAULT NULL,
  `id_uv` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom_complet`, `email`) VALUES
(1, 'Amandine Lemoine', 'amandine.lemoine@email.com'),
(2, 'Benoit Girard', 'benoit.girard@email.com'),
(3, 'Celine Durand', 'celine.durand@email.com'),
(4, 'Dylan Mercier', 'dylan.mercier@email.com'),
(5, 'Elena Dupont', 'elena.dupont@email.com'),
(6, 'Florian Benoit', 'florian.benoit@email.com'),
(7, 'Geraldine Lemoine', 'geraldine.lemoine@email.com'),
(8, 'Hugo Thomas', 'hugo.thomas@email.com'),
(9, 'Ingrid Benoit', 'ingrid.benoit@email.com'),
(10, 'Jules Pires', 'jules.pires@email.com'),
(11, 'Kevin Dupont', 'kevin.dupont@email.com'),
(12, 'Lea Girard', 'lea.girard@email.com'),
(13, 'Mickael Dufresne', 'mickael.dufresne@email.com'),
(14, 'Nathalie Pires', 'nathalie.pires@email.com'),
(15, 'Oscar Thomas', 'oscar.thomas@email.com'),
(16, 'Pauline Lemoine', 'pauline.lemoine@email.com'),
(17, 'Quentin Benoit', 'quentin.benoit@email.com'),
(18, 'Romain Girard', 'romain.girard@email.com'),
(19, 'Sophie Mercier', 'sophie.mercier@email.com'),
(20, 'Thomas Dupont', 'thomas.dupont@email.com'),
(21, 'Ursula Thomas', 'ursula.thomas@email.com'),
(22, 'Valentin Pires', 'valentin.pires@email.com'),
(23, 'William Durand', 'william.durand@email.com'),
(24, 'Xavier Benoit', 'xavier.benoit@email.com'),
(25, 'Yasmine Lemoine', 'yasmine.lemoine@email.com'),
(26, 'Zacharie Girard', 'zacharie.girard@email.com'),
(27, 'Alice Thomas', 'alice.thomas@email.com'),
(28, 'Benjamin Dupont', 'benjamin.dupont@email.com'),
(29, 'Cecile Mercier', 'cecile.mercier@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `feed`
--

CREATE TABLE `feed` (
  `id` int(255) NOT NULL,
  `id_utilisateur` int(255) NOT NULL,
  `type` enum('new_uv','new_message','new_fichier') DEFAULT NULL,
  `id_posts` int(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `feed`
--

INSERT INTO `feed` (`id`, `id_utilisateur`, `type`, `id_posts`, `date_creation`) VALUES
(51, 1, 'new_message', 22, '2025-04-06 21:51:21'),
(52, 1, 'new_message', 2, '2025-04-06 21:51:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscription`
--

CREATE TABLE `inscription` (
  `id_utilisateur` int(11) NOT NULL,
  `id_uv` int(11) NOT NULL,
  `date_inscription` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `inscription`
--

INSERT INTO `inscription` (`id_utilisateur`, `id_uv`, `date_inscription`) VALUES
(1, 1, '2025-02-17'),
(1, 2, '2025-02-17'),
(1, 3, '2025-02-17'),
(1, 4, '2025-02-17'),
(2, 1, '2025-02-17'),
(2, 2, '2025-02-17'),
(2, 3, '2025-02-17'),
(2, 4, '2025-02-17'),
(3, 1, '2025-02-17'),
(3, 2, '2025-02-17'),
(3, 3, '2025-02-17'),
(3, 4, '2025-02-17'),
(4, 1, '2025-02-17'),
(4, 2, '2025-02-17'),
(4, 3, '2025-02-17'),
(4, 4, '2025-02-17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `note`
--

CREATE TABLE `note` (
  `id_note` int(11) NOT NULL,
  `valeur` float DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `id_soumission` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id_posts` int(255) NOT NULL,
  `id_utilisateur` int(255) NOT NULL,
  `id_uv` int(255) NOT NULL,
  `corps` text DEFAULT NULL,
  `nom_fichier` varchar(255) DEFAULT NULL,
  `date_creation` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `posts`
--

INSERT INTO `posts` (`id_posts`, `id_utilisateur`, `id_uv`, `corps`, `nom_fichier`, `date_creation`) VALUES
(2, 4, 2, 'Post 2', NULL, '2025-03-31'),
(22, 1, 2, 'Post 1', NULL, '2025-03-31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `professeurs`
--

CREATE TABLE `professeurs` (
  `id` int(11) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professeurs`
--

INSERT INTO `professeurs` (`id`, `nom_complet`, `email`) VALUES
(1, 'Alice Dupont', 'alice.dupont@email.com'),
(2, 'Bertrand Martin', 'bertrand.martin@email.com'),
(3, 'Celine Durand', 'celine.durand@email.com'),
(4, 'David Lemoine', 'david.lemoine@email.com'),
(5, 'Eva Lemoine', 'eva.lemoine@email.com'),
(6, 'Francois Girard', 'francois.girard@email.com'),
(7, 'Gabriel Dufresne', 'gabriel.dufresne@email.com'),
(8, 'Helene Lemoine', 'helene.lemoine@email.com'),
(9, 'Isabelle Benoit', 'isabelle.benoit@email.com'),
(10, 'Jacques Thomas', 'jacques.thomas@email.com'),
(11, 'Karine Pires', 'karine.pires@email.com'),
(12, 'Louis Mercier', 'louis.mercier@email.com'),
(13, 'Michel Pires', 'michel.pires@email.com'),
(14, 'Nathalie Lemoine', 'nathalie.lemoine@email.com'),
(15, 'Olivier Dufresne', 'olivier.dufresne@email.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ressource`
--

CREATE TABLE `ressource` (
  `id_ressource` int(11) NOT NULL,
  `titre` varchar(150) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `url_fichier` varchar(255) DEFAULT NULL,
  `id_uv` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `role`
--

INSERT INTO `role` (`id_role`, `nom`) VALUES
(1, 'Etudiants'),
(2, 'Professeurs'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Estrutura para tabela `soumission`
--

CREATE TABLE `soumission` (
  `id_soumission` int(11) NOT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `date_envoi` datetime DEFAULT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_devoir` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `td1`
--

CREATE TABLE `td1` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `td1`
--

INSERT INTO `td1` (`id`, `name`) VALUES
(1, 'Alice'),
(2, 'Bob'),
(3, 'Charlie'),
(4, 'David'),
(5, 'Eve'),
(6, 'Frank'),
(7, 'Grace'),
(8, 'Hannah'),
(9, 'Isaac'),
(10, 'Jack'),
(11, 'Karen'),
(12, 'Leo'),
(13, 'Mia'),
(14, 'Nathan'),
(15, 'Olivia'),
(16, 'Paul'),
(17, 'Quincy'),
(18, 'Rachel'),
(19, 'Sam'),
(20, 'Tina'),
(21, 'Umar'),
(22, 'Violet'),
(23, 'William'),
(24, 'Xander'),
(25, 'Yasmin'),
(26, 'Zane'),
(27, 'Aaron'),
(28, 'Bella'),
(29, 'Chris'),
(30, 'Diana'),
(31, 'Ethan'),
(32, 'Fiona'),
(33, 'George'),
(34, 'Holly'),
(35, 'Ian'),
(36, 'Jenna'),
(37, 'Kyle'),
(38, 'Liam'),
(39, 'Monica'),
(40, 'Noah'),
(41, 'Oscar'),
(42, 'Penelope'),
(43, 'Quentin'),
(44, 'Rebecca'),
(45, 'Steven'),
(46, 'Tracy'),
(47, 'Uma'),
(48, 'Victor'),
(49, 'Wendy'),
(50, 'Xavier'),
(51, 'Zelda'),
(52, 'Adrian'),
(53, 'Beatrice'),
(54, 'Caleb'),
(55, 'Daisy'),
(56, 'Elijah'),
(57, 'Felicia'),
(58, 'Gordon'),
(59, 'Hazel'),
(60, 'Ivan'),
(61, 'Jasmine'),
(62, 'Keith'),
(63, 'Laura'),
(64, 'Martin'),
(65, 'Nina'),
(66, 'Owen'),
(67, 'Phoebe'),
(68, 'Quinn'),
(69, 'Roger'),
(70, 'Sophia'),
(71, 'Thomas'),
(72, 'Ursula'),
(73, 'Vincent'),
(74, 'Willow'),
(75, 'Xenia'),
(76, 'Yusuf'),
(77, 'Zara'),
(78, 'Albert'),
(79, 'Bianca'),
(80, 'Cedric'),
(81, 'Delilah'),
(82, 'Emmanuel'),
(83, 'Frida'),
(84, 'Gavin'),
(85, 'Helena'),
(86, 'Ibrahim'),
(87, 'Juliet'),
(88, 'Kelvin'),
(89, 'Lillian'),
(90, 'Mason'),
(91, 'Norah'),
(92, 'Orlando'),
(93, 'Patricia'),
(94, 'Quinton'),
(95, 'Ronald'),
(96, 'Sienna'),
(97, 'Tristan'),
(98, 'Ulrich'),
(99, 'Vanessa'),
(100, 'Walter'),
(101, 'Ximena'),
(102, 'Yvette'),
(103, 'Zachary'),
(104, 'Amelia'),
(105, 'Bryan');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ue`
--

CREATE TABLE `ue` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `intitule` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ue`
--

INSERT INTO `ue` (`id`, `code`, `intitule`, `image`) VALUES
(1, 'WE4A', 'Développement Web', 'https://2.bp.blogspot.com/-U5UbLbjuQvM/Wp-Pb-34TnI/AAAAAAAAAJg/bwLpWnM_L0E90DkN8OpzIm5ol8axYtcugCLcBGAs/s1600/7%2B1webdevelopment_1600x1200_021014.jpg'),
(2, 'SY43', 'Base de Données', 'https://www.mobibiz.in/blog/wp-content/uploads/2023/01/Development-process-of-android-app-1024x1024.png'),
(3, 'SI40', 'Intelligence Artificielle', 'https://image2.slideserve.com/4368604/syst-me-d-information7-l.jpg'),
(4, 'LS03', 'Espagnol niveau B2', 'https://moncompte-personnel-formation.fr/wp-content/uploads/2023/04/devenir-bilingue-espagnol-400x189.jpg'),
(5, 'SY44', 'Administration systèmes et réseaux', 'https://images.tuto.net/ui/thematics/40/administrateur-reseaux.jpg'),
(6, 'WE4B', 'Technologies WEB avancées', 'https://www.gostudy.rs/wp-content/uploads/2023/03/Sta-je-Internet_1-min-768x505.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ues`
--

CREATE TABLE `ues` (
  `ID` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ues`
--

INSERT INTO `ues` (`ID`, `title`, `code`) VALUES
(2, 'Advanced Information Systems', 'IA41'),
(3, 'Database Management Systems', 'UO80'),
(4, 'Web Development for Information Systems', 'SY44'),
(5, 'Network Security in Information Systems', 'IA41'),
(6, 'Data Warehousing and Business Intelligence', 'UO80'),
(7, 'Information Systems Analysis and Design', 'SY44'),
(8, 'Cloud Computing for Information Systems', 'IA41'),
(9, 'Mobile Application Development for Information Systems', 'UO80'),
(10, 'Project Management for Information Systems', 'SY44'),
(11, 'This is the new', 'NEW4');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `email`) VALUES
(1, 'Alice', 'Smith', 'alice.smith@example.com'),
(2, 'Bob', 'Johnson', 'bob.johnson@example.com'),
(3, 'Charlie', 'Williams', 'charlie.williams@example.com'),
(4, 'David', 'Brown', 'david.brown@example.com'),
(5, 'Eve', 'Jones', 'eve.jones@example.com'),
(6, 'Frank', 'Davis', 'frank.davis@example.com'),
(7, 'Grace', 'Miller', 'grace.miller@example.com'),
(8, 'Henry', 'Wilson', 'henry.wilson@example.com'),
(9, 'Ivy', 'Moore', 'ivy.moore@example.com'),
(10, 'Jack', 'Taylor', 'jack.taylor@example.com'),
(11, 'Katie', 'Anderson', 'katie.anderson@example.com'),
(12, 'Liam', 'Thomas', 'liam.thomas@example.com'),
(13, 'Mia', 'Jackson', 'mia.jackson@example.com'),
(14, 'Noah', 'White', 'noah.white@example.com'),
(15, 'Olivia', 'Harris', 'olivia.harris@example.com'),
(16, 'Peter', 'Martin', 'peter.martin@example.com'),
(17, 'Quinn', 'Thompson', 'quinn.thompson@example.com'),
(18, 'Ryan', 'Garcia', 'ryan.garcia@example.com'),
(19, 'Sophia', 'Martinez', 'sophia.martinez@example.com'),
(20, 'Tom', 'Robinson', 'tom.robinson@example.com'),
(21, 'Uma', 'Clark', 'uma.clark@example.com'),
(22, 'Victor', 'Rodriguez', 'victor.rodriguez@example.com'),
(23, 'Wendy', 'Lewis', 'wendy.lewis@example.com'),
(24, 'Xavier', 'Lee', 'xavier.lee@example.com'),
(25, 'Yara', 'Walker', 'yara.walker@example.com'),
(26, 'Zachary', 'Hall', 'zachary.hall@example.com'),
(27, 'Ava', 'Allen', 'ava.allen@example.com'),
(28, 'Benjamin', 'Young', 'benjamin.young@example.com'),
(29, 'Chloe', 'Hernandez', 'chloe.hernandez@example.com'),
(30, 'Daniel', 'King', 'daniel.king@example.com'),
(31, 'Ella', 'Wright', 'ella.wright@example.com'),
(32, 'Finley', 'Lopez', 'finley.lopez@example.com'),
(33, 'Georgia', 'Hill', 'georgia.hill@example.com'),
(34, 'Hudson', 'Scott', 'hudson.scott@example.com'),
(35, 'Isla', 'Green', 'isla.green@example.com'),
(36, 'Jasper', 'Adams', 'jasper.adams@example.com'),
(37, 'Lily', 'Baker', 'lily.baker@example.com'),
(38, 'Mason', 'Gonzalez', 'mason.gonzalez@example.com'),
(39, 'Nora', 'Nelson', 'nora.nelson@example.com'),
(40, 'Owen', 'Carter', 'owen.carter@example.com'),
(41, 'patrick', 'aguiar', 'dwadwadwa'),
(42, 'patrickdwdwdwdw', 'aguiar', 'dwadwadwadwadwa'),
(43, 'new', 'teste', 'patrick@utbm.fr');

-- --------------------------------------------------------

--
-- Estrutura para tabela `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `id_role`) VALUES
(1, 'Tran', 'Kevin', 'kevin.tran@utbm.fr', '1234', 1),
(2, 'Voisin', 'Florent', 'florent.voisin@utbm.fr', '1234', 1),
(3, 'Atechian', 'Theo', 'theo.atechian@utbm.fr', '1234', 1),
(4, 'YADAV RANGEL DE AGUIAR', 'Patrick', 'patrick.yadav-rangel-de-aguiar@utbm.fr', '1234', 1),
(5, 'Brunoud', 'Alexandre', 'alexandre.brunoud@utbm.fr', '1234', 2),
(6, 'Lombard', 'Alexandre', 'alexandre.lombard@utbm.fr', '1234', 2),
(7, 'Litzler', 'Antoine', 'antoine.litzler@utbm.fr', '1234', 2),
(8, 'Benkirane', 'Fatima Ez Zahra', 'fatima.benkirane@utbm.fr', '1234', 2),
(9, 'admin', 'admin', 'admin@utbm.fr', '1234', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `uv`
--

CREATE TABLE `uv` (
  `id_uv` int(11) NOT NULL,
  `nom_uv` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `uv`
--

INSERT INTO `uv` (`id_uv`, `nom_uv`, `description`) VALUES
(1, 'WE4A/B', 'Technologie et programmation web'),
(2, 'SI40', 'Système d\'information'),
(3, 'ID01', 'CrunchTime'),
(4, 'ST40/ST50', 'Départ en stage');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `devoir`
--
ALTER TABLE `devoir`
  ADD PRIMARY KEY (`id_devoir`),
  ADD KEY `id_uv` (`id_uv`);

--
-- Índices de tabela `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_posts` (`id_posts`);

--
-- Índices de tabela `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id_utilisateur`,`id_uv`),
  ADD KEY `id_uv` (`id_uv`);

--
-- Índices de tabela `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id_note`),
  ADD KEY `id_soumission` (`id_soumission`);

--
-- Índices de tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_posts`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `fk_posts_uv` (`id_uv`);

--
-- Índices de tabela `professeurs`
--
ALTER TABLE `professeurs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ressource`
--
ALTER TABLE `ressource`
  ADD PRIMARY KEY (`id_ressource`),
  ADD KEY `id_uv` (`id_uv`);

--
-- Índices de tabela `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Índices de tabela `soumission`
--
ALTER TABLE `soumission`
  ADD PRIMARY KEY (`id_soumission`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_devoir` (`id_devoir`);

--
-- Índices de tabela `td1`
--
ALTER TABLE `td1`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ue`
--
ALTER TABLE `ue`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Índices de tabela `ues`
--
ALTER TABLE `ues`
  ADD PRIMARY KEY (`ID`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- Índices de tabela `uv`
--
ALTER TABLE `uv`
  ADD PRIMARY KEY (`id_uv`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `devoir`
--
ALTER TABLE `devoir`
  MODIFY `id_devoir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `feed`
--
ALTER TABLE `feed`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `note`
--
ALTER TABLE `note`
  MODIFY `id_note` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id_posts` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `professeurs`
--
ALTER TABLE `professeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `ressource`
--
ALTER TABLE `ressource`
  MODIFY `id_ressource` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `soumission`
--
ALTER TABLE `soumission`
  MODIFY `id_soumission` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `td1`
--
ALTER TABLE `td1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de tabela `ue`
--
ALTER TABLE `ue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `ues`
--
ALTER TABLE `ues`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de tabela `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `uv`
--
ALTER TABLE `uv`
  MODIFY `id_uv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `devoir`
--
ALTER TABLE `devoir`
  ADD CONSTRAINT `devoir_ibfk_1` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`);

--
-- Restrições para tabelas `feed`
--
ALTER TABLE `feed`
  ADD CONSTRAINT `id_posts` FOREIGN KEY (`id_posts`) REFERENCES `posts` (`id_posts`);

--
-- Restrições para tabelas `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`);

--
-- Restrições para tabelas `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `note_ibfk_1` FOREIGN KEY (`id_soumission`) REFERENCES `soumission` (`id_soumission`);

--
-- Restrições para tabelas `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_uv` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Restrições para tabelas `ressource`
--
ALTER TABLE `ressource`
  ADD CONSTRAINT `ressource_ibfk_1` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`);

--
-- Restrições para tabelas `soumission`
--
ALTER TABLE `soumission`
  ADD CONSTRAINT `soumission_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `soumission_ibfk_2` FOREIGN KEY (`id_devoir`) REFERENCES `devoir` (`id_devoir`);

--
-- Restrições para tabelas `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
