K rozchození projektu si vytvořte prázdnou datábazi.
Potom spusťíte tento skript:

-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Počítač: localhost:3306
-- Vytvořeno: Ned 23. dub 2023, 22:47
-- Verze serveru: 10.3.37-MariaDB-0ubuntu0.20.04.1
-- Verze PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `c147ip3`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `job` varchar(255) NOT NULL,
  `wage` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `key`
--

CREATE TABLE `key` (
  `key_id` int(11) NOT NULL,
  `employee` int(11) NOT NULL,
  `room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `no` varchar(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `room` (`room`);

--
-- Klíče pro tabulku `key`
--
ALTER TABLE `key`
  ADD PRIMARY KEY (`key_id`),
  ADD UNIQUE KEY `employee_room` (`employee`,`room`),
  ADD KEY `room` (`room`);

--
-- Klíče pro tabulku `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `no` (`no`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `key`
--
ALTER TABLE `key`
  MODIFY `key_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`room`) REFERENCES `room` (`room_id`);

--
-- Omezení pro tabulku `key`
--
ALTER TABLE `key`
  ADD CONSTRAINT `key_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employee` (`employee_id`),
  ADD CONSTRAINT `key_ibfk_2` FOREIGN KEY (`room`) REFERENCES `room` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


A finalní krok je, že si přepíšete v složce config soubor config.json tak, aby údaje byly stejné jako ty vaše (příklad: místo 'your-username' dáte svoje uživatelské jméno do databáze.)
