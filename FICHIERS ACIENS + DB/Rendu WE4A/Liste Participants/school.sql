-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 06 avr. 2025 à 22:44
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
-- Base de données : `school`
--

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants`
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
-- Structure de la table `professeurs`
--

CREATE TABLE `professeurs` (
  `id` int(11) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `professeurs`
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

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `professeurs`
--
ALTER TABLE `professeurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `professeurs`
--
ALTER TABLE `professeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
