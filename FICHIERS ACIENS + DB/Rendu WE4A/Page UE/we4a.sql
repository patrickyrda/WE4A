-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 06 avr. 2025 à 21:36
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `we4a`
--

-- --------------------------------------------------------

--
-- Structure de la table `devoir`
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
-- Structure de la table `feed`
--

CREATE TABLE `feed` (
  `id` int(255) NOT NULL,
  `id_utilisateur` int(255) NOT NULL,
  `type` enum('new_uv','new_message','new_fichier') DEFAULT NULL,
  `id_posts` int(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `feed`
--

INSERT INTO `feed` (`id`, `id_utilisateur`, `type`, `id_posts`, `date_creation`) VALUES
(2, 4, 'new_message', 2, '2025-03-31 09:27:00'),
(48, 1, 'new_message', 22, '2025-03-31 10:32:28');

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

CREATE TABLE `inscription` (
  `id_utilisateur` int(11) NOT NULL,
  `id_uv` int(11) NOT NULL,
  `date_inscription` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscription`
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
-- Structure de la table `note`
--

CREATE TABLE `note` (
  `id_note` int(11) NOT NULL,
  `valeur` float DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `id_soumission` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
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
-- Déchargement des données de la table `posts`
--

INSERT INTO `posts` (`id_posts`, `id_utilisateur`, `id_uv`, `corps`, `nom_fichier`, `date_creation`) VALUES
(2, 4, 2, 't', NULL, '2025-03-31'),
(22, 1, 2, 'blabalttt', NULL, '2025-03-31');

-- --------------------------------------------------------

--
-- Structure de la table `ressource`
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
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `nom`) VALUES
(1, 'Etudiants'),
(2, 'Professeurs'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Structure de la table `soumission`
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
-- Structure de la table `utilisateur`
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
-- Déchargement des données de la table `utilisateur`
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
-- Structure de la table `uv`
--

CREATE TABLE `uv` (
  `id_uv` int(11) NOT NULL,
  `nom_uv` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `uv`
--

INSERT INTO `uv` (`id_uv`, `nom_uv`, `description`) VALUES
(1, 'WE4A/B', 'Technologie et programmation web'),
(2, 'SI40', 'Système d\'information'),
(3, 'ID01', 'CrunchTime'),
(4, 'ST40/ST50', 'Départ en stage');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `devoir`
--
ALTER TABLE `devoir`
  ADD PRIMARY KEY (`id_devoir`),
  ADD KEY `id_uv` (`id_uv`);

--
-- Index pour la table `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_posts` (`id_posts`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id_utilisateur`,`id_uv`),
  ADD KEY `id_uv` (`id_uv`);

--
-- Index pour la table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id_note`),
  ADD KEY `id_soumission` (`id_soumission`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_posts`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `fk_posts_uv` (`id_uv`);

--
-- Index pour la table `ressource`
--
ALTER TABLE `ressource`
  ADD PRIMARY KEY (`id_ressource`),
  ADD KEY `id_uv` (`id_uv`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Index pour la table `soumission`
--
ALTER TABLE `soumission`
  ADD PRIMARY KEY (`id_soumission`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_devoir` (`id_devoir`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- Index pour la table `uv`
--
ALTER TABLE `uv`
  ADD PRIMARY KEY (`id_uv`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `devoir`
--
ALTER TABLE `devoir`
  MODIFY `id_devoir` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `feed`
--
ALTER TABLE `feed`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `note`
--
ALTER TABLE `note`
  MODIFY `id_note` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `posts`
--
ALTER TABLE `posts`
  MODIFY `id_posts` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `ressource`
--
ALTER TABLE `ressource`
  MODIFY `id_ressource` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `soumission`
--
ALTER TABLE `soumission`
  MODIFY `id_soumission` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `uv`
--
ALTER TABLE `uv`
  MODIFY `id_uv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `devoir`
--
ALTER TABLE `devoir`
  ADD CONSTRAINT `devoir_ibfk_1` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`);

--
-- Contraintes pour la table `feed`
--
ALTER TABLE `feed`
  ADD CONSTRAINT `id_posts` FOREIGN KEY (`id_posts`) REFERENCES `posts` (`id_posts`);

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`);

--
-- Contraintes pour la table `note`
--
ALTER TABLE `note`
  ADD CONSTRAINT `note_ibfk_1` FOREIGN KEY (`id_soumission`) REFERENCES `soumission` (`id_soumission`);

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_uv` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`);

--
-- Contraintes pour la table `ressource`
--
ALTER TABLE `ressource`
  ADD CONSTRAINT `ressource_ibfk_1` FOREIGN KEY (`id_uv`) REFERENCES `uv` (`id_uv`);

--
-- Contraintes pour la table `soumission`
--
ALTER TABLE `soumission`
  ADD CONSTRAINT `soumission_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`),
  ADD CONSTRAINT `soumission_ibfk_2` FOREIGN KEY (`id_devoir`) REFERENCES `devoir` (`id_devoir`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
