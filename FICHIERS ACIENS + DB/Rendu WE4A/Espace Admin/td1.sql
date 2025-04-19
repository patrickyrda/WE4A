-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/04/2025 às 21:25
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

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `td1`
--
ALTER TABLE `td1`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `td1`
--
ALTER TABLE `td1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
