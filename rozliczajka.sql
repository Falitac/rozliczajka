-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 10 Kwi 2022, 22:33
-- Wersja serwera: 10.4.22-MariaDB
-- Wersja PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `rozliczajka`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `bank_nr` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `bank_nr`, `user_id`) VALUES
(1, '08116022020000000353304148', 14);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `value` bigint(20) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `items`
--

INSERT INTO `items` (`id`, `name`, `value`, `receipt_id`) VALUES
(1, 'Nektar', 494, NULL),
(4, 'Budyń', 91, NULL),
(5, 'K. BatMusli', 132, NULL),
(6, 'Jaffa', 170, NULL),
(18, 'Apis miód', 3999, 20),
(19, 'Muller', 594, 20),
(20, 'Mleko 2,1%', 304, 21),
(21, 'Orzeszki', 570, 21),
(22, 'Mascarpone', 1046, 21),
(23, 'Pomarańcze', 262, 21),
(24, 'Popcorn', 369, 22),
(25, 'Twaróg', 1136, 22),
(26, 'Muller', 275, 22),
(27, 'Ser', 1094, 22),
(28, 'Herbata piramidka', 349, 22),
(29, 'Crunchips', 569, 26),
(30, 'Paluszki', 279, 26),
(31, 'Serek wiejski', 256, 26),
(32, 'Drożdżówka', 250, 26),
(33, 'Płatki', 239, 27),
(34, 'Serek', 256, 27),
(35, 'Pierogi z mięsem', 760, 27),
(36, 'Muller', 275, 27),
(37, 'Orzeszki', 570, 27),
(38, 'Zmywacz', 699, 27),
(39, 'Syrop', 380, 28),
(40, 'K. BatMusli', 132, 30),
(41, 'Jaffa', 170, 30),
(42, 'Nektar', 495, 31),
(43, 'Budyń', 91, 31),
(44, 'Budyń czekoladowy', 91, 31),
(45, 'Galaretka', 179, 31),
(46, 'Jogurt', 208, 31),
(47, 'Serek', 256, 31),
(48, 'Pomarańcze', 244, 31),
(49, 'Ziemia', 397, 32),
(50, 'Jaja', 713, 32),
(51, 'Orzeszki', 570, 32),
(52, 'Papryka słodka', 598, 32),
(53, 'Serek', 256, 32),
(54, 'Cebula', 760, 32),
(55, 'Jabłko Szampion', 416, 32),
(56, 'Pomidory', 1142, 32),
(57, 'Banan', 345, 32),
(58, 'Chleb', 499, 32),
(59, 'Torilla W', 280, 33),
(60, 'Tortilla F', 190, 33),
(61, 'Chrupki', 598, 37),
(62, 'Bagietka', 228, 37),
(63, 'Wafelki', 664, 37),
(64, 'Banan', 377, 37),
(65, 'Sorbet', 1199, 37),
(66, 'Herbata', 649, 37),
(67, 'Muller', 275, 37),
(68, 'Zelki', 299, 37),
(69, 'Serek wiejski', 256, 37),
(70, 'Lody tiramisu', 523, 37);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `item_payers`
--

CREATE TABLE `item_payers` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `item_payers`
--

INSERT INTO `item_payers` (`id`, `item_id`, `person_id`) VALUES
(1, 4, 14),
(2, 1, 14),
(3, 5, 1),
(4, 6, 1),
(9, 18, 12),
(10, 19, 14),
(11, 20, 12),
(12, 21, 12),
(13, 22, 14),
(14, 23, 14),
(15, 24, 13),
(16, 25, 13),
(17, 26, 14),
(18, 27, 13),
(19, 27, 12),
(20, 27, 14),
(21, 27, 1),
(22, 28, 13),
(23, 29, 14),
(24, 30, 14),
(25, 31, 14),
(26, 32, 14),
(27, 33, 14),
(28, 34, 14),
(29, 35, 13),
(30, 36, 14),
(31, 37, 12),
(32, 38, 14),
(33, 39, 14),
(34, 40, 1),
(35, 41, 1),
(36, 42, 14),
(37, 43, 14),
(38, 44, 14),
(39, 45, 14),
(40, 46, 14),
(41, 47, 14),
(42, 48, 14),
(43, 49, 14),
(44, 50, 14),
(45, 50, 1),
(46, 50, 13),
(47, 50, 12),
(48, 51, 14),
(49, 52, 14),
(50, 52, 1),
(51, 52, 13),
(52, 52, 12),
(53, 53, 14),
(54, 54, 14),
(55, 54, 1),
(56, 54, 13),
(57, 54, 12),
(58, 55, 14),
(59, 55, 1),
(60, 55, 13),
(61, 55, 12),
(62, 56, 14),
(63, 56, 1),
(64, 56, 13),
(65, 56, 12),
(66, 57, 13),
(67, 58, 14),
(68, 58, 1),
(69, 58, 13),
(70, 58, 12),
(71, 59, 12),
(72, 60, 14),
(73, 61, 14),
(74, 62, 14),
(75, 63, 14),
(76, 64, 13),
(77, 65, 14),
(78, 65, 13),
(79, 66, 14),
(80, 67, 14),
(81, 68, 14),
(82, 69, 14),
(83, 70, 14),
(84, 70, 13);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `value` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `payments`
--

INSERT INTO `payments` (`id`, `receipt_id`, `user_id`, `paid`, `value`) VALUES
(38, 20, 1, 0, 4177),
(39, 20, 14, 0, 4771),
(40, 20, 12, 0, 8176),
(41, 20, 13, 0, 4177),
(42, 21, 14, 0, 1308),
(43, 21, 12, 0, 874),
(44, 22, 13, 0, 2506),
(45, 22, 12, 0, 652),
(46, 22, 14, 0, 927),
(47, 22, 1, 0, 652),
(48, 22, 15, 0, 379),
(49, 23, 14, 0, 182),
(50, 23, 12, 0, 182),
(51, 23, 1, 0, 182),
(52, 23, 13, 0, 182),
(53, 24, 1, 0, 293),
(54, 24, 12, 0, 293),
(55, 24, 13, 0, 293),
(56, 24, 14, 0, 293),
(57, 25, 13, 0, 279),
(58, 25, 14, 0, 279),
(59, 25, 12, 0, 279),
(60, 25, 1, 0, 279),
(61, 25, 15, 0, 279),
(62, 26, 14, 0, 2196),
(63, 26, 12, 0, 842),
(64, 26, 1, 0, 842),
(65, 26, 13, 0, 842),
(66, 27, 14, 0, 3287),
(67, 27, 12, 0, 2388),
(68, 27, 1, 0, 1818),
(69, 27, 13, 0, 2578),
(70, 28, 14, 0, 871),
(71, 28, 12, 0, 491),
(72, 28, 1, 0, 491),
(73, 28, 13, 0, 491),
(74, 29, 1, 0, 1328),
(75, 29, 12, 0, 1328),
(76, 29, 13, 0, 1328),
(77, 29, 14, 0, 1328),
(78, 30, 1, 0, 1075),
(79, 30, 12, 0, 773),
(80, 30, 13, 0, 773),
(81, 30, 14, 0, 773),
(82, 31, 14, 0, 2854),
(83, 31, 12, 0, 1290),
(84, 31, 1, 0, 1290),
(85, 31, 13, 0, 1290),
(86, 32, 14, 0, 2302),
(87, 32, 1, 0, 1079),
(88, 32, 13, 0, 1424),
(89, 32, 12, 0, 1079),
(90, 32, 15, 0, 49),
(91, 33, 14, 0, 414),
(92, 33, 12, 0, 504),
(93, 33, 13, 0, 224),
(94, 33, 1, 0, 224),
(96, 35, 14, 0, 128),
(97, 35, 13, 0, 128),
(98, 35, 12, 0, 128),
(99, 35, 1, 0, 128),
(100, 37, 14, 0, 4096),
(101, 37, 12, 0, 267),
(102, 37, 13, 0, 1504),
(103, 37, 1, 0, 267);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `price` int(11) NOT NULL,
  `payer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `receipts`
--

INSERT INTO `receipts` (`id`, `date`, `price`, `payer_id`) VALUES
(20, '2022-07-04', 21302, 1),
(21, '2022-04-05', 2182, 14),
(22, '2022-04-02', 5120, 13),
(23, '2022-04-01', 730, 14),
(24, '2022-03-31', 1173, 1),
(25, '2022-07-04', 1399, 13),
(26, '2022-03-26', 4725, 14),
(27, '2022-03-29', 10071, 14),
(28, '2022-03-24', 2347, 14),
(29, '2022-03-14', 5312, 1),
(30, '2022-03-17', 3394, 1),
(31, '2022-03-19', 6724, 14),
(32, '2022-03-21', 5945, 14),
(33, '2022-03-23', 1369, 14),
(35, '2022-03-30', 512, 14),
(37, '2022-08-04', 6136, 14);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `name`, `password`) VALUES
(1, 'Szymek', '$2y$10$GOvHm9FIJJPivhl3sSprgeSGWBA4q8qrtMGvDl4OgMRSfk5rWF7cu'),
(12, 'Wojtek', '$2y$10$a9lK4nBfQQ2Zo2rbfA5JDeKe8AuPPyvLI.ZZqmfZsurEby84rjDTG'),
(13, 'Badyl', '$2y$10$JeDuNorrOZV7A80i54W0sONeb8s55w9EjNOTVkI41ipqu6bsZZdyO'),
(14, 'Filek', '$2y$10$zcpY6vwYsc3s.4um8HjX1e10Ge5/EZxQICCwrzPjJReGlFhcQcyhS'),
(15, 'Ania', '$2y$10$LtWQf5fgjK8fSZszYNgtK.b5B3J6h.M/JwR.F5lV0fFQl4yBFxEkK');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id-index` (`receipt_id`);

--
-- Indeksy dla tabeli `item_payers`
--
ALTER TABLE `item_payers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indeksy dla tabeli `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `payer_id` (`payer_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT dla tabeli `item_payers`
--
ALTER TABLE `item_payers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT dla tabeli `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT dla tabeli `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `item_payers`
--
ALTER TABLE `item_payers`
  ADD CONSTRAINT `item_payers_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `item_payers_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`payer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
