-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 10 Lut 2022, 20:03
-- Wersja serwera: 8.0.26
-- Wersja PHP: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `s401354`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Categories`
--

CREATE TABLE `Categories` (
  `idCategory` int NOT NULL,
  `categoryName` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Posts`
--

CREATE TABLE `Posts` (
  `idPost` int NOT NULL,
  `title` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `createdTime` datetime NOT NULL,
  `imgSource` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localization` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `isSold` tinyint NOT NULL,
  `idUser` int NOT NULL,
  `idCategory` int NOT NULL
) ;

--
-- Wyzwalacze `Posts`
--
DELIMITER $$
CREATE TRIGGER `deleteMessage` BEFORE DELETE ON `Posts` FOR EACH ROW BEGIN
	DELETE FROM UserPostMessage WHERE idPost = OLD.idPost;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `UserPostMessage`
--

CREATE TABLE `UserPostMessage` (
  `idUserPostMessage` int NOT NULL,
  `message` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idUserSender` int NOT NULL,
  `idUserReceiver` int NOT NULL,
  `idPost` int NOT NULL,
  `createdTimeMessage` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Users`
--

CREATE TABLE `Users` (
  `idUser` int NOT NULL,
  `login` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`idCategory`),
  ADD UNIQUE KEY `idCategory` (`idCategory`),
  ADD UNIQUE KEY `categoryName` (`categoryName`);

--
-- Indeksy dla tabeli `Posts`
--
ALTER TABLE `Posts`
  ADD PRIMARY KEY (`idPost`),
  ADD UNIQUE KEY `idPost` (`idPost`),
  ADD KEY `Posts_fk0` (`idUser`),
  ADD KEY `Posts_fk1` (`idCategory`);

--
-- Indeksy dla tabeli `UserPostMessage`
--
ALTER TABLE `UserPostMessage`
  ADD PRIMARY KEY (`idUserPostMessage`),
  ADD UNIQUE KEY `idUserPostMessage` (`idUserPostMessage`),
  ADD KEY `UserPostMessage_fk0` (`idUserSender`),
  ADD KEY `UserPostMessage_fk1` (`idPost`),
  ADD KEY `UserPostMessage_fk2` (`idUserReceiver`);

--
-- Indeksy dla tabeli `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`idUser`),
  ADD UNIQUE KEY `idUser` (`idUser`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `Categories`
--
ALTER TABLE `Categories`
  MODIFY `idCategory` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `Posts`
--
ALTER TABLE `Posts`
  MODIFY `idPost` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `UserPostMessage`
--
ALTER TABLE `UserPostMessage`
  MODIFY `idUserPostMessage` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT dla tabeli `Users`
--
ALTER TABLE `Users`
  MODIFY `idUser` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `Posts`
--
ALTER TABLE `Posts`
  ADD CONSTRAINT `Posts_fk0` FOREIGN KEY (`idUser`) REFERENCES `Users` (`idUser`),
  ADD CONSTRAINT `Posts_fk1` FOREIGN KEY (`idCategory`) REFERENCES `Categories` (`idCategory`);

--
-- Ograniczenia dla tabeli `UserPostMessage`
--
ALTER TABLE `UserPostMessage`
  ADD CONSTRAINT `UserPostMessage_fk0` FOREIGN KEY (`idUserSender`) REFERENCES `Users` (`idUser`),
  ADD CONSTRAINT `UserPostMessage_fk1` FOREIGN KEY (`idPost`) REFERENCES `Posts` (`idPost`),
  ADD CONSTRAINT `UserPostMessage_fk2` FOREIGN KEY (`idUserReceiver`) REFERENCES `Users` (`idUser`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
