-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 01. Sep 2017 um 17:10
-- Server-Version: 10.1.13-MariaDB
-- PHP-Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `anubarak16`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `answer`
--

CREATE TABLE `answer` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `enabled` int(11) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `answer`
--

INSERT INTO `answer` (`answer_id`, `question_id`, `text`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`) VALUES
(1, NULL, 'wefwefwefwe', 'ee', 1, 1, '2017-07-20 10:15:25', '2017-07-20 10:15:25', 'ergergergergre'),
(3, NULL, 'wefwefwefwe', 'ergerger', 1, 1, '2017-07-20 10:15:39', '2017-07-20 10:15:39', 'ergergergergre'),
(4, 0, 'Neuer Text', 'Antwort', 0, 1, '2017-07-20 10:20:49', '2017-08-03 10:37:10', 'Antwort'),
(5, 0, 'regerger', 'HÃ¤', 0, 0, '2017-07-20 10:21:00', '2017-07-20 10:21:00', 'HÃ¤'),
(6, NULL, 'Hier kÃ¶nnte Ihre Werbung stehen', 'Title-fÃ¼r-Answer', 1, 1, '2017-07-26 10:39:19', '2017-07-26 10:39:19', 'Title fÃ¼r Answer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `assets`
--

CREATE TABLE `assets` (
  `asset_id` int(11) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `enabled` int(11) DEFAULT '1',
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `assets`
--

INSERT INTO `assets` (`asset_id`, `path`, `name`, `enabled`, `createDate`, `updateDate`, `title`) VALUES
(1, '/upload/pic.obj14500-lang4.jpg', 'Bild mit namen ^.^', 1, '2017-07-19 08:54:30', '2017-07-19 08:54:30', 'Ein Asset'),
(6, '/upload/dscn2115.jpg', 'Bild mit namen ^.^', 1, '2017-07-19 14:59:39', '2017-07-19 14:59:39', 'Ein Asset'),
(7, '/upload/popup_thumbnail.jpg', NULL, 1, '2017-08-03 08:42:08', '2017-08-03 08:42:08', 'popup_thumbnail'),
(8, '/upload/pic.obj14500-lang4.jpg', 'pic.obj14500-lang4', 1, '2017-08-03 08:46:43', '2017-08-03 08:46:43', 'pic.obj14500-lang4'),
(9, '/upload/pic.obj14500-lang4.jpg', 'pic.obj14500-lang4', 1, '2017-08-03 08:47:52', '2017-08-03 08:47:52', 'pic.obj14500-lang4'),
(10, '/upload/1501746768.jpg', '1501746768', 1, '2017-08-03 09:52:48', '2017-08-03 09:52:48', '1501746768'),
(11, '/upload/1501747177.jpg', '1501747177', 1, '2017-08-03 09:59:37', '2017-08-03 09:59:37', '1501747177'),
(12, '/upload/1501747218.jpg', '1501747218', 1, '2017-08-03 10:00:18', '2017-08-03 10:00:18', '1501747218'),
(13, '/upload/1501747236.jpg', '1501747236', 1, '2017-08-03 10:00:36', '2017-08-03 10:00:36', '1501747236'),
(14, '/upload/1501747259.jpg', '1501747259', 1, '2017-08-03 10:00:59', '2017-08-03 10:00:59', '1501747259'),
(15, '/upload/1501747319.jpg', '1501747319', 1, '2017-08-03 10:01:59', '2017-08-03 10:01:59', '1501747319'),
(16, '/upload/1501747335.jpg', '1501747335', 1, '2017-08-03 10:02:15', '2017-08-03 10:02:15', '1501747335'),
(17, '/upload/blkimg-01 (1).jpg', 'blkimg-01 (1)', 1, '2017-08-29 13:23:02', '2017-08-29 13:23:02', 'blkimg-01 (1)');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comic`
--

CREATE TABLE `comic` (
  `comic_id` int(11) NOT NULL,
  `text` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `comic`
--

INSERT INTO `comic` (`comic_id`, `text`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`) VALUES
(1, 'Ein neuer Comic....', 'The-Avengers', 1, 1, '2017-08-29 11:00:00', '2017-08-29 12:03:48', 'The Avengers');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `folder`
--

CREATE TABLE `folder` (
  `folder_id` int(11) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `enabled` int(11) DEFAULT '1',
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `matrix`
--

CREATE TABLE `matrix` (
  `matrix_id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `handle` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `content` text,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `enabled` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `matrix`
--

INSERT INTO `matrix` (`matrix_id`, `type`, `handle`, `position`, `content`, `createDate`, `updateDate`, `enabled`) VALUES
(1, 'text', 'testMatrix', 2, '{"text":"zewites Gespeichert 3","headline":"zweites"}', '2017-08-23 09:50:23', '2017-08-23 10:26:52', 1),
(2, 'text', 'testMatrix', 0, '{"text":"Position 2","headline":"Position 2"}', '2017-08-23 10:17:17', '2017-08-23 10:26:52', 1),
(3, 'module', 'testMatrix', 1, '{"headline":"Ein Modul dazwischen"}', '2017-08-23 10:22:52', '2017-08-23 10:26:52', 1),
(4, 'text', 'testMatrix', 1, '{"text":"Ich bin ein Test....  nur ein Test","headline":""}', '2017-08-24 08:35:37', '2017-08-24 14:53:15', 1),
(5, 'text', 'testMatrix', 0, '{"text":"initial save","headline":""}', '2017-08-24 08:43:29', '2017-08-24 14:53:15', 1),
(6, 'text', 'testMatrix', 0, '{"text":"Title","headline":"Headline"}', '2017-08-24 09:18:34', '2017-08-24 09:18:34', 1),
(9, 'module', 'testMatrix', 0, '{"headline":"Headline","test_id":["3","6"]}', '2017-08-28 10:23:36', '2017-08-29 13:23:08', 1),
(10, 'text', 'testMatrix', 1, '{"headline":"headline neuer Text","text":"<p>Text neuer Textgregerg rergereg e<img src=\\"http:\\/\\/localhost:8080\\/framework\\/?asset=17\\" class=\\"ql-embed-selected\\" width=\\"169\\" style=\\"cursor: nwse-resize;\\"><\\/p>"}', '2017-08-28 15:55:03', '2017-08-29 13:23:08', 1),
(11, 'boolean', 'testMatrix', 2, '{"headline":"checkbox checked","checkbox":true}', '2017-08-29 13:20:15', '2017-08-29 13:23:08', 1),
(12, 'text', 'testMatrix', 0, '{"headline":"Ein Modul","text":"<p>Text eines Modules<\\/p>"}', '2017-08-30 10:28:41', '2017-08-30 10:28:41', 1),
(13, 'text', 'testMatrix', 0, '{"headline":"Jetzt noch mit Modul","text":"<p>Jetzt noch mit Modul.....<\\/p>"}', '2017-08-30 10:32:33', '2017-08-30 10:55:11', 1),
(14, 'module', 'testMatrix', 1, '{"headline":"Es klappt sogar ein Modul mit relations","test_id":["1"]}', '2017-08-30 10:33:07', '2017-08-30 10:55:11', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `page`
--

CREATE TABLE `page` (
  `page_id` int(11) NOT NULL,
  `linkName` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `page`
--

INSERT INTO `page` (`page_id`, `linkName`, `position`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`) VALUES
(1, 'Seite 1 Link', 0, 'Seite-1', 1, 1, '2017-07-27 07:30:41', '2017-08-23 11:55:05', 'gehookter Title'),
(2, 'Seite 2', 1, 'Seite-2', 1, 1, '2017-07-27 07:33:20', '2017-08-23 15:27:14', 'Seite 2'),
(3, 'Unterseite 1', 0, 'Unterseite-1', 1, 1, '2017-07-27 08:32:20', '2017-08-04 12:06:29', 'Unterseite 1'),
(4, 'Unterseite 2', 0, 'Unterseite-2', 1, 1, '2017-07-28 07:25:46', '2017-08-23 15:25:04', 'Unterseite 2'),
(5, NULL, 0, 'Unterseite-3', 1, 1, '2017-07-28 09:56:31', '2017-08-23 15:24:03', 'Unterseite-3'),
(6, NULL, 4, 'Unterseite-4', 1, 1, '2017-07-28 09:56:48', '2017-08-23 15:26:59', 'Unterseite 4'),
(7, 'fewewf', 2, 'Unterunterseite', 1, 1, '2017-07-28 13:02:57', '2017-08-04 12:06:33', 'Unterunterseite'),
(9, NULL, 1, 'Unterseite-4-1', 1, 1, '2017-07-31 11:51:13', '2017-08-23 15:23:30', 'Unterseite 4 1'),
(10, NULL, 3, 'BenutzerSeite-direktes-update', 1, 1, '2017-08-04 09:59:24', '2017-08-23 15:27:05', 'BenutzerSeite direktes update');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `question`
--

CREATE TABLE `question` (
  `question_id` int(11) NOT NULL,
  `text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `correctAnswer` int(11) DEFAULT NULL,
  `pointsPlus` int(11) DEFAULT NULL,
  `pointsMinus` int(11) DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Daten für Tabelle `question`
--

INSERT INTO `question` (`question_id`, `text`, `correctAnswer`, `pointsPlus`, `pointsMinus`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`, `position`) VALUES
(1, 'text', 2, 3, 3, 'Frage', 5, 1, '2017-07-18 20:00:00', '2017-08-02 12:26:19', 'Unterste', 5),
(2, '<p>texttrtr</p>', 2, 3, 3, 'Frage-2', 7, 1, '2017-07-19 09:29:34', '2017-08-31 09:46:10', 'Frage rrthtr', 0),
(3, '<p><img src="http://localhost:8080/framework/?asset=9" width="148">text<img src="http://localhost:8080/framework/?asset=16" class="ql-embed-selected" width="453" style="cursor: nwse-resize;"></p>', 2, 3, 3, 'Frage-3', 0, 1, '2017-07-18 16:20:17', '2017-08-03 10:05:32', 'Frage 3', 3),
(4, 'text', 2, 3, 3, 'Frage-1', 0, 0, '2017-07-19 08:40:35', '2017-08-03 10:42:59', 'Ausgestellte Frage', 12),
(5, 'text', 2, 3, 3, 'Frage-4', 0, 1, '2017-07-19 08:54:53', '2017-07-19 08:54:53', 'Frage', 6),
(6, 'text', 2, 3, 3, 'Frage-5', 0, 1, '2017-07-19 08:56:50', '2017-08-03 11:00:22', 'Frage', 4),
(7, 'text', 2, 3, 3, 'Frage-6', 0, 1, '2017-07-19 08:56:51', '2017-07-19 08:56:51', 'Frage', 7),
(8, 'text', 2, 3, 3, 'Frage-7', 0, 1, '2017-07-19 08:56:52', '2017-07-19 08:56:52', 'Frage', 8),
(9, 'text', 2, 3, 3, 'Frage-8', 2, 1, '2017-07-19 09:02:16', '2017-07-19 09:02:16', 'Frage', 10),
(10, 'text', 2, 3, 3, 'Neue-Frage-neu-gespeichert', 1, 1, '2017-07-20 11:46:30', '2017-07-20 11:46:30', 'Neue Frage neu gespeichert', 11),
(11, '<p>textrrere</p>', 2, 3, 3, 'Frage-bearbeitet', 1, 1, '2017-07-20 11:47:13', '2017-08-29 13:23:09', 'Frage bearbeitet', 2),
(12, 'text', 2, 3, 3, 'Frage-mit-nur-einer-programmierten-Relation', 1, 0, '2017-07-20 12:17:48', '2017-08-04 14:10:45', 'Frage mit nur einer programmierten Relation', 9),
(13, '<p>Jetzt aber bitte</p>', 2, 3, 1, 'komplett-neu-mit-angular', 1, 1, '2017-07-25 17:15:28', '2017-08-30 10:54:14', 'Frage-2', 1),
(15, '<p>Ein toller Text</p>', NULL, NULL, NULL, 'Trying-to-save-another-one', 1, 1, '2017-08-24 07:11:22', '2017-08-24 09:18:34', 'Trying to save another one', 13);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `records`
--

INSERT INTO `records` (`id`, `name`, `table_name`, `date`) VALUES
(15, 'question', 'question', '2017-07-18 16:19:46'),
(16, 'answer', 'answer', '2017-07-20 10:25:04'),
(19, 'page', 'page', '2017-07-27 09:16:26'),
(20, 'matrix', 'matrix', '2017-08-21 13:51:20'),
(22, 'comic', 'comic', '2017-08-29 11:42:06');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `relation`
--

CREATE TABLE `relation` (
  `id` int(11) NOT NULL,
  `field_1` varchar(64) NOT NULL,
  `id_1` int(11) NOT NULL,
  `model_1` varchar(64) NOT NULL,
  `field_2` varchar(64) NOT NULL,
  `id_2` int(11) NOT NULL,
  `model_2` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `relation`
--

INSERT INTO `relation` (`id`, `field_1`, `id_1`, `model_1`, `field_2`, `id_2`, `model_2`) VALUES
(7, 'test_id', 10, 'question', 'answer_id', 1, 'answer'),
(8, 'test_id', 10, 'question', 'answer_id', 3, 'answer'),
(259, 'parent_pid', 8, 'page', 'page_id', 6, 'page'),
(348, 'test_id', 1, 'question', 'answer_id', 1, 'answer'),
(349, 'test_id', 1, 'question', 'answer_id', 4, 'answer'),
(351, 'test_id', 4, 'question', 'answer_id', 3, 'answer'),
(352, 'test_id', 4, 'question', 'answer_id', 4, 'answer'),
(437, 'test_id', 12, 'question', 'answer_id', 3, 'answer'),
(528, 'parent_pid', 1, 'page', 'page_id', 5, 'page'),
(532, 'parent_pid', 9, 'page', 'page_id', 10, 'page'),
(535, 'parent_pid', 5, 'page', 'page_id', 10, 'page'),
(536, 'parent_pid', 4, 'page', 'page_id', 6, 'page'),
(574, 'matrix', 15, 'question', 'matrix_id', 6, 'matrix'),
(627, 'test_id', 9, 'matrix', 'answer_id', 3, 'answer'),
(628, 'test_id', 9, 'matrix', 'answer_id', 6, 'answer'),
(629, 'matrix', 11, 'question', 'matrix_id', 9, 'matrix'),
(630, 'matrix', 11, 'question', 'matrix_id', 10, 'matrix'),
(631, 'matrix', 11, 'question', 'matrix_id', 11, 'matrix'),
(634, 'pages', 12, 'question', 'page_id', 1, 'page'),
(635, 'pages', 12, 'question', 'page_id', 10, 'page'),
(636, 'matrix', 12, 'question', 'matrix_id', 12, 'matrix'),
(677, 'pages', 13, 'question', 'page_id', 1, 'page'),
(678, 'pages', 13, 'question', 'page_id', 5, 'page'),
(679, 'pages', 13, 'question', 'page_id', 9, 'page'),
(680, 'test_id', 14, 'matrix', 'answer_id', 1, 'answer'),
(681, 'matrix', 13, 'question', 'matrix_id', 13, 'matrix'),
(682, 'matrix', 13, 'question', 'matrix_id', 14, 'matrix'),
(683, 'test_id', 2, 'question', 'answer_id', 6, 'answer');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userpermission`
--

CREATE TABLE `userpermission` (
  `permission_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `permission` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `userpermission`
--

INSERT INTO `userpermission` (`permission_id`, `user_id`, `permission`) VALUES
(1, 2, 'question.update');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` text,
  `email` varchar(255) DEFAULT NULL,
  `enabled` int(11) DEFAULT '1',
  `admin` int(11) DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `enabled`, `admin`, `password`, `createDate`, `updateDate`, `title`) VALUES
(1, 'Vorname', 'Nachname', 'anubarak1993@gmail.com', 1, 1, '$2y$10$7Bu7/CC1IMHwKGC2Cy8Wxeh996rY.8YdpVa7UastIP5VnHQNPaKkK', '2017-07-19 16:47:08', '2017-07-19 17:34:31', 'Title'),
(4, '0', '0', 'anubarak16@gmail.com', 1, 0, '$2y$10$9g/JQDVzN9o0CLLSNL/IM.alCxkYHmCu9pzsLk.SRnlfLTQwBcWhe', '2017-08-03 17:30:27', '2017-08-03 17:30:27', 'Robin');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`answer_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indizes für die Tabelle `comic`
--
ALTER TABLE `comic`
  ADD PRIMARY KEY (`comic_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `folder`
--
ALTER TABLE `folder`
  ADD PRIMARY KEY (`folder_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `matrix`
--
ALTER TABLE `matrix`
  ADD PRIMARY KEY (`matrix_id`);

--
-- Indizes für die Tabelle `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`question_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `relation`
--
ALTER TABLE `relation`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `userpermission`
--
ALTER TABLE `userpermission`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `answer`
--
ALTER TABLE `answer`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT für Tabelle `assets`
--
ALTER TABLE `assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT für Tabelle `comic`
--
ALTER TABLE `comic`
  MODIFY `comic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `folder`
--
ALTER TABLE `folder`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `matrix`
--
ALTER TABLE `matrix`
  MODIFY `matrix_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT für Tabelle `page`
--
ALTER TABLE `page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `question`
--
ALTER TABLE `question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT für Tabelle `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT für Tabelle `relation`
--
ALTER TABLE `relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=684;
--
-- AUTO_INCREMENT für Tabelle `userpermission`
--
ALTER TABLE `userpermission`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
