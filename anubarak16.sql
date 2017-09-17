-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2017 at 08:49 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anubarak16`
--

-- --------------------------------------------------------

--
-- Table structure for table `answer`
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
  `title` varchar(255) DEFAULT NULL,
  `entryType` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`answer_id`, `question_id`, `text`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`, `entryType`) VALUES
(1, NULL, 'wefwefwefwe', 'ee', 1, 1, '2017-07-20 10:15:25', '2017-07-20 10:15:25', 'ID 1', ''),
(3, NULL, 'wefwefwefwe', 'ergerger', 1, 1, '2017-07-20 10:15:39', '2017-07-20 10:15:39', 'ergergergergre', ''),
(4, 0, 'Neuer Text', 'Antwort', 0, 1, '2017-07-20 10:20:49', '2017-08-03 10:37:10', 'Antwort', ''),
(5, 0, 'regerger', 'HÃ¤', 0, 0, '2017-07-20 10:21:00', '2017-07-20 10:21:00', 'HÃ¤', ''),
(6, NULL, 'Hier kÃ¶nnte Ihre Werbung stehen', 'Title-fÃ¼r-Answer', 1, 1, '2017-07-26 10:39:19', '2017-07-26 10:39:19', 'Title fÃ¼r Answer', ''),
(7, 5, 'gipÃ¶uhÃ¶', 'Slug-night', 1, 1, '2017-07-19 09:29:34', '2017-09-02 13:10:37', 'Wenn das geht bringe ich mich um', ''),
(8, 32, 'wefwfw', 'whups', 1, 1, '2017-07-19 09:29:34', '2017-09-02 13:14:25', 'Whups', ''),
(9, 34, 'ergreger', 'erggrege', 1, 1, '2017-07-19 09:29:34', '2017-09-02 13:19:06', 'ergergrefd', ''),
(10, 3, 'ewfwef', 'dynamisch', 1, 1, '2017-07-19 09:29:34', '2017-09-02 17:42:10', 'dynamisch', ''),
(11, 3, '4ewfwe', 'eeee', 1, 1, '2017-07-19 09:29:34', '2017-09-02 19:27:01', 'seew', '');

-- --------------------------------------------------------

--
-- Table structure for table `assets`
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
-- Dumping data for table `assets`
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
(17, '/upload/blkimg-01 (1).jpg', 'blkimg-01 (1)', 1, '2017-08-29 13:23:02', '2017-08-29 13:23:02', 'blkimg-01 (1)'),
(18, '/upload/Autodesk-logo.png', 'Autodesk-logo', 1, '2017-09-08 13:35:29', '2017-09-08 13:35:29', 'Autodesk-logo'),
(19, '/upload/Autodesk-logo.png', 'Autodesk-logo', 1, '2017-09-08 13:36:03', '2017-09-08 13:36:03', 'Autodesk-logo'),
(20, '/upload/1504870578.jpg', '1504870578', 1, '2017-09-08 13:36:18', '2017-09-08 13:36:18', '1504870578'),
(21, '/upload/1348094.png', '1348094', 1, '2017-09-09 16:04:32', '2017-09-09 16:04:32', '1348094'),
(22, '/upload/BlenderDesktopLogo.png', 'BlenderDesktopLogo', 1, '2017-09-12 08:01:13', '2017-09-12 08:01:13', 'BlenderDesktopLogo');

-- --------------------------------------------------------

--
-- Table structure for table `comic`
--

CREATE TABLE `comic` (
  `comic_id` int(11) NOT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL,
  `text` text,
  `author_id` float DEFAULT '0',
  `enabled` tinyint(1) DEFAULT NULL,
  `slug` varchar(255) DEFAULT '',
  `lnk_answer` tinyint(1) NOT NULL,
  `entryType` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comic`
--

INSERT INTO `comic` (`comic_id`, `createDate`, `updateDate`, `title`, `text`, `author_id`, `enabled`, `slug`, `lnk_answer`, `entryType`) VALUES
(1, '2017-08-29 11:00:00', '2017-09-06 22:07:45', 'The Avengers', NULL, 0, 1, 'comic-1', 0, 'comic'),
(2, '2017-09-08 19:26:42', '2017-09-08 19:27:08', 'Noch ein Comic', NULL, 0, 1, 'comic-2', 0, 'comic');

-- --------------------------------------------------------

--
-- Table structure for table `entrytypes`
--

CREATE TABLE `entrytypes` (
  `id` int(11) NOT NULL,
  `recordHandle` varchar(255) DEFAULT NULL,
  `handle` varchar(255) DEFAULT NULL,
  `label` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `entrytypes`
--

INSERT INTO `entrytypes` (`id`, `recordHandle`, `handle`, `label`) VALUES
(1, 'comic', 'comic', 'Comic'),
(2, 'rezept', 'rezept', 'Rezepte'),
(3, 'page', 'page', 'Seiten'),
(4, 'ingredient', 'ingredient', 'Zutaten'),
(5, 'ingredient', 'bonus', 'Zusatzzutaten'),
(6, 'ingredient', 'newFieldType', 'Neuer FeldTyp');

-- --------------------------------------------------------

--
-- Table structure for table `fieldlayout`
--

CREATE TABLE `fieldlayout` (
  `id` int(11) NOT NULL,
  `fieldHandle` varchar(255) DEFAULT NULL,
  `recordHandle` varchar(255) DEFAULT NULL,
  `tabId` int(64) NOT NULL,
  `entryType` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fieldlayout`
--

INSERT INTO `fieldlayout` (`id`, `fieldHandle`, `recordHandle`, `tabId`, `entryType`) VALUES
(90, 'comic_id', 'comic', 1, 'comic'),
(93, 'text', 'comic', 1, 'comic'),
(95, 'lnk_answer', 'comic', 1, 'comic'),
(121, 'receipe', 'rezept', 2, 'rezept'),
(122, 'lnk_ingredients', 'rezept', 2, 'rezept'),
(123, 'description', 'ingredient', 1, 'bonus'),
(141, 'lnk_answer', 'ingredient', 4, 'ingredient'),
(142, 'text', 'ingredient', 3, 'ingredient'),
(153, 'text', 'ingredient', 5, 'newFieldType'),
(154, 'number', 'ingredient', 5, 'newFieldType'),
(155, 'text_normal', 'ingredient', 5, 'newFieldType');

-- --------------------------------------------------------

--
-- Table structure for table `fieldlayouttabs`
--

CREATE TABLE `fieldlayouttabs` (
  `id` int(11) NOT NULL,
  `handle` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fieldlayouttabs`
--

INSERT INTO `fieldlayouttabs` (`id`, `handle`, `label`, `position`) VALUES
(1, 'tab1', 'Allgemeines', 1),
(2, 'tabNr1', 'Rezeptinformationen', 1),
(3, 'tab1', 'SEO', 2),
(4, 'tab2', 'Tab 2', 1),
(5, 'Field-Layout tab', 'Field Layout tab', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `fieldType` varchar(255) NOT NULL,
  `settings` text,
  `enabled` tinyint(1) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fields`
--

INSERT INTO `fields` (`id`, `slug`, `fieldType`, `settings`, `enabled`, `createDate`, `updateDate`, `title`) VALUES
(1, 'text', 'text', '{"0":"text","relatedTo":"15","minLength":"0","maxLength":"100","min_len":10,"max_len":100}', 1, '2017-09-05 05:08:08', '2017-09-12 07:13:58', 'Text'),
(3, 'comic_id', 'number', '{"0":"hidden","index":"primarykey"}', 1, '2017-09-05 07:52:52', '2017-09-05 07:52:52', 'Comic ID'),
(7, 'lnk_answer', 'relation', '{"relatedTo":{"table":"answer","field":"answer_id","model":"answer"},"0":"relation"}', 1, '2017-09-06 11:39:58', '2017-09-06 13:41:04', 'VerknÃ¼pfung Antwort'),
(8, 'testField', 'mixed', '{"relatedTo":"15","minLength":"0","maxLength":"100","0":"text"}', 1, '2017-09-08 04:36:04', '2017-09-08 06:36:35', 'Test Feld'),
(10, 'receipe', 'text', '{"0":"text","min_len":5,"max_len":16,"title":"Text"}', 1, '2017-09-09 14:13:41', '2017-09-09 14:14:07', 'Rezept'),
(11, 'description', 'text', '{"0":"text","min_len":5,"max_len":16,"title":"Text"}', 1, '2017-09-09 14:14:16', '2017-09-09 14:14:49', 'Beschreibung'),
(13, 'lnk_ingredients', 'relation', '{"relatedTo":{"table":"ingredients","field":"id","model":"ingredient"},"0":"relation"}', 1, '2017-09-09 19:57:57', '2017-09-09 20:06:16', 'Zutaten VerknÃ¼pfung'),
(14, 'number', 'number', '{"0":"number","depth":3,"min_numeric":10,"max_numeric":50}', 1, '2017-09-11 15:13:01', '2017-09-12 06:50:11', 'Zahl'),
(15, 'text_normal', 'mixed', '{"min_len":10,"max_len":20,"0":"mixed"}', 1, '2017-09-12 07:15:48', '2017-09-12 07:29:56', 'Text unformatiert');

-- --------------------------------------------------------

--
-- Table structure for table `folder`
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
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `entryType` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `lnk_answer` tinyint(1) DEFAULT '0',
  `comic_id` float DEFAULT '0',
  `number` float DEFAULT '0',
  `description` text,
  `text` text,
  `text_normal` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `slug`, `author_id`, `enabled`, `entryType`, `title`, `createDate`, `updateDate`, `lnk_answer`, `comic_id`, `number`, `description`, `text`, `text_normal`) VALUES
(1, 'Gurke', 1, 1, 'bonus', 'Gurke', '2017-09-09 20:04:03', '2017-09-11 07:44:54', 0, 0, 0, NULL, NULL, NULL),
(2, 'Tomate', 1, 1, 'ingredient', 'Tomate', '2017-09-09 20:04:20', '2017-09-09 20:04:27', 0, 0, 0, NULL, NULL, NULL),
(3, 'Banane', 1, 1, 'ingredient', 'Banane', '2017-09-09 17:00:09', '2017-09-09 20:18:35', 0, 0, 0, NULL, NULL, NULL),
(4, 'Neuer-FeldTyp', 1, 1, 'newFieldType', 'Neuer FeldTyp', '2017-09-12 06:08:55', '2017-09-12 07:27:28', 0, 0, 43, '', '<p>Text	</p>', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `matrix`
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
-- Dumping data for table `matrix`
--

INSERT INTO `matrix` (`matrix_id`, `type`, `handle`, `position`, `content`, `createDate`, `updateDate`, `enabled`) VALUES
(2, 'text', 'testMatrix', 0, '{"headline":"Position 2","text":"Position 2"}', '2017-08-23 10:17:17', '2017-09-03 21:40:37', 1),
(3, 'module', 'testMatrix', 1, '{"headline":"Ein Modul dazwischen"}', '2017-08-23 10:22:52', '2017-08-23 10:26:52', 1),
(4, 'text', 'testMatrix', 1, '{"text":"Ich bin ein Test....  nur ein Test","headline":""}', '2017-08-24 08:35:37', '2017-08-24 14:53:15', 1),
(5, 'text', 'testMatrix', 0, '{"text":"initial save","headline":""}', '2017-08-24 08:43:29', '2017-08-24 14:53:15', 1),
(6, 'text', 'testMatrix', 0, '{"text":"Title","headline":"Headline"}', '2017-08-24 09:18:34', '2017-08-24 09:18:34', 1),
(9, 'module', 'testMatrix', 0, '{"headline":"Headline","test_id":["3","6"]}', '2017-08-28 10:23:36', '2017-08-29 13:23:08', 1),
(10, 'text', 'testMatrix', 1, '{"headline":"headline neuer Text","text":"<p>Text neuer Textgregerg rergereg e<img src=\\"http:\\/\\/localhost:8080\\/framework\\/?asset=17\\" class=\\"ql-embed-selected\\" width=\\"169\\" style=\\"cursor: nwse-resize;\\"><\\/p>"}', '2017-08-28 15:55:03', '2017-08-29 13:23:08', 1),
(11, 'boolean', 'testMatrix', 2, '{"headline":"checkbox checked","checkbox":true}', '2017-08-29 13:20:15', '2017-08-29 13:23:08', 1),
(12, 'text', 'testMatrix', 0, '{"headline":"Ein Modul","text":"<p>Text eines Modules<\\/p>"}', '2017-08-30 10:28:41', '2017-08-30 10:28:41', 1),
(13, 'text', 'testMatrix', 0, '{"headline":"Jetzt noch mit Modul","text":"<p>Jetzt noch mit Modul.....<\\/p>"}', '2017-08-30 10:32:33', '2017-08-30 10:55:11', 1),
(14, 'module', 'testMatrix', 1, '{"headline":"Es klappt sogar ein Modul mit relations","test_id":["1"]}', '2017-08-30 10:33:07', '2017-08-30 10:55:11', 1),
(15, 'comic', 'testMatrix', 0, '{"headline":"Some Headline2","comic":["1"]}', '2017-09-03 11:29:09', '2017-09-03 20:53:42', 1),
(16, 'text', 'testMatrix', 0, '{"headline":"Ein Text","text":"<p>Hoffentlich wird das was.... v.v<\\/p>"}', '2017-09-03 21:05:35', '2017-09-03 21:05:35', 1),
(17, 'text', 'testMatrix', 1, '{"headline":"Die headline war weg v.v","text":"<p>Wo zum Teufel ist die Matrix hin?<\\/p>"}', '2017-09-03 21:39:41', '2017-09-03 21:40:37', 1),
(18, 'text', 'testMatrix', 0, '{"headline":"Bitte","text":"<p>Schreibe eine Matrix<\\/p>"}', '2017-09-05 19:58:32', '2017-09-05 19:59:59', 1),
(19, 'text', 'testMatrix', 1, '{"headline":"novh","text":"<p>uhblbhn<\\/p>"}', '2017-09-05 19:59:59', '2017-09-05 19:59:59', 1),
(20, 'text', 'testMatrix', 0, '{"headline":"ergegerg","text":"<p>ergergerger<\\/p>"}', '2017-09-09 19:48:13', '2017-09-09 19:48:13', 1),
(21, 'boolean', 'testMatrix', 1, '{"headline":"ergregerger","checkbox":true}', '2017-09-09 19:48:13', '2017-09-09 19:48:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `page`
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
  `title` varchar(255) DEFAULT NULL,
  `entryType` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`page_id`, `linkName`, `position`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`, `entryType`) VALUES
(1, 'Seite 1 Link', 0, 'Seite-1', 1, 1, '2017-07-27 07:30:41', '2017-09-04 07:29:49', 'gehookter Title', ''),
(2, 'Seite 2', 1, 'Seite-2', 1, 1, '2017-07-27 07:33:20', '2017-09-04 07:20:54', 'Seite 2', ''),
(3, 'Unterseite 1', 0, 'Unterseite-1', 1, 1, '2017-07-27 08:32:20', '2017-09-04 07:17:04', 'Unterseite 1', ''),
(4, 'Unterseite 2', 0, 'Unterseite-2', 1, 1, '2017-07-28 07:25:46', '2017-08-23 15:25:04', 'Unterseite 2', ''),
(5, NULL, 0, 'Unterseite-3', 1, 1, '2017-07-28 09:56:31', '2017-08-23 15:24:03', 'Unterseite-3', ''),
(6, NULL, 4, 'Unterseite-4', 1, 1, '2017-07-28 09:56:48', '2017-08-23 15:26:59', 'Unterseite 4', ''),
(7, 'fewewf', 2, 'Unterunterseite', 1, 1, '2017-07-28 13:02:57', '2017-09-04 07:44:15', 'Unterunterseite', ''),
(9, NULL, 1, 'Unterseite-4-1', 1, 1, '2017-07-31 11:51:13', '2017-08-23 15:23:30', 'Unterseite 4 1', ''),
(10, NULL, 3, 'BenutzerSeite-direktes-update', 1, 1, '2017-08-04 09:59:24', '2017-09-04 07:42:52', 'BenutzerSeite direktes update', '');

-- --------------------------------------------------------

--
-- Table structure for table `question`
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
  `position` int(11) NOT NULL,
  `entryType` varchar(64) COLLATE utf8_german2_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`question_id`, `text`, `correctAnswer`, `pointsPlus`, `pointsMinus`, `slug`, `author_id`, `enabled`, `createDate`, `updateDate`, `title`, `position`, `entryType`) VALUES
(1, '<p>texte</p>', 2, 3, 3, 'Frage', 5, 1, '2017-07-18 20:00:00', '2017-09-05 19:59:59', 'Unterste', 4, ''),
(2, '<p><strong>Fetter</strong></p>', 2, 3, 3, 'Frage-2', 7, 1, '2017-07-19 09:29:34', '2017-09-03 21:40:37', 'Frage bearbeitet mit Fields', 14, ''),
(3, '<p><img src="http://localhost:8080/framework/?asset=9" width="148">text<img src="http://localhost:8080/framework/?asset=16" class="ql-embed-selected" width="453" style="cursor: nwse-resize;"></p>', 2, 3, 3, 'Frage-3', 0, 1, '2017-07-18 16:20:17', '2017-08-03 10:05:32', 'Frage 3', 2, ''),
(4, 'text', 2, 3, 3, 'Frage-1', 0, 0, '2017-07-19 08:40:35', '2017-08-03 10:42:59', 'Ausgestellte Frage', 11, ''),
(5, 'text', 2, 3, 3, 'Frage-4', 0, 1, '2017-07-19 08:54:53', '2017-07-19 08:54:53', 'Frage', 5, ''),
(6, 'text', 2, 3, 3, 'Frage-5', 0, 1, '2017-07-19 08:56:50', '2017-08-03 11:00:22', 'Frage', 3, ''),
(7, 'text', 2, 3, 3, 'Frage-6', 0, 1, '2017-07-19 08:56:51', '2017-07-19 08:56:51', 'Frage', 6, ''),
(8, 'text', 2, 3, 3, 'Frage-7', 0, 1, '2017-07-19 08:56:52', '2017-07-19 08:56:52', 'Frage', 7, ''),
(9, 'text', 2, 3, 3, 'Frage-8', 2, 1, '2017-07-19 09:02:16', '2017-07-19 09:02:16', 'Frage', 9, ''),
(10, 'text', 2, 3, 3, 'Neue-Frage-neu-gespeichert', 1, 1, '2017-07-20 11:46:30', '2017-07-20 11:46:30', 'Neue Frage neu gespeichert', 10, ''),
(11, '<p>textrrere</p>', 2, 3, 3, 'Frage-bearbeitet', 1, 1, '2017-07-20 11:47:13', '2017-09-09 19:48:13', 'Frage bearbeitet', 0, 'question'),
(12, 'text', 2, 3, 3, 'Frage-mit-nur-einer-programmierten-Relation', 1, 0, '2017-07-20 12:17:48', '2017-08-04 14:10:45', 'Frage mit nur einer programmierten Relation', 8, ''),
(13, '<p>Jetzt aber bitte</p>', 2, 3, 1, 'komplett-neu-mit-angular', 1, 1, '2017-07-25 17:15:28', '2017-09-04 06:32:33', 'Frage-2', 1, ''),
(15, '<p>Ein toller Text</p>', NULL, NULL, NULL, 'Trying-to-save-another-one', 1, 1, '2017-08-24 07:11:22', '2017-08-24 09:18:34', 'Trying to save another one', 12, ''),
(16, NULL, 3, 4, 3, 'omg', 0, 1, '2017-09-03 19:03:48', '2017-09-03 21:05:35', 'omg', 13, '');

-- --------------------------------------------------------

--
-- Table structure for table `receipes`
--

CREATE TABLE `receipes` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  `entryType` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `createDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `updateDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `receipe` text,
  `lnk_ingredients` tinyint(1) DEFAULT '0',
  `lnk_answer` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `receipes`
--

INSERT INTO `receipes` (`id`, `slug`, `author_id`, `enabled`, `entryType`, `title`, `createDate`, `updateDate`, `receipe`, `lnk_ingredients`, `lnk_answer`) VALUES
(1, 'Erstes-Rezept', 1, 1, 'rezept', 'Erstes Rezept', '2017-09-09 17:00:09', '2017-09-10 10:45:16', '<p>Text	</p>', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `handle` varchar(255) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `structure` varchar(16) NOT NULL,
  `primary_key` varchar(255) NOT NULL,
  `template` varchar(256) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `name`, `handle`, `table_name`, `structure`, `primary_key`, `template`, `date`) VALUES
(15, 'question', 'question', 'question', 'channel', 'question_id', '', '2017-07-18 16:19:46'),
(16, 'answer', 'answer', 'answer', 'channel', 'answer_id', '', '2017-07-20 10:25:04'),
(19, 'Seiten', 'page', 'page', 'channel', 'page_id', '', '2017-07-27 09:16:26'),
(22, 'Comic', 'comic', 'comic', 'channel', 'comic_id', '', '2017-08-29 11:42:06'),
(26, 'Rezepte', 'rezept', 'receipes', 'channel', 'id', '', '2017-09-09 16:49:47'),
(27, 'Zutaten', 'ingredient', 'ingredients', 'channel', 'id', 'zutaten/index.twig', '2017-09-09 19:57:49');

-- --------------------------------------------------------

--
-- Table structure for table `relation`
--

CREATE TABLE `relation` (
  `id` int(11) NOT NULL,
  `fieldHandle` varchar(64) NOT NULL,
  `id_1` int(11) NOT NULL,
  `record_1` varchar(64) NOT NULL,
  `id_2` int(11) NOT NULL,
  `record_2` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `relation`
--

INSERT INTO `relation` (`id`, `fieldHandle`, `id_1`, `record_1`, `id_2`, `record_2`) VALUES
(574, 'matrix', 15, 'question', 6, 'matrix'),
(634, 'pages', 12, 'question', 1, 'page'),
(635, 'pages', 12, 'question', 10, 'page'),
(636, 'matrix', 12, 'question', 12, 'matrix'),
(677, 'pages', 13, 'question', 1, 'page'),
(678, 'pages', 13, 'question', 5, 'page'),
(679, 'pages', 13, 'question', 9, 'page'),
(691, 'pages', 1, 'comic', 1, 'page'),
(692, 'pages', 1, 'comic', 2, 'page'),
(812, 'comic', 15, 'matrix', 1, 'comic'),
(817, 'matrix', 16, 'question', 16, 'matrix'),
(823, 'matrix', 2, 'question', 2, 'matrix'),
(824, 'matrix', 2, 'question', 17, 'matrix'),
(827, 'answer', 13, 'question', 6, 'answer'),
(828, 'answer', 13, 'question', 7, 'answer'),
(829, 'parent', 3, 'page', 2, 'page'),
(830, 'page_id', 2, 'page', 1, 'page'),
(831, 'parent', 10, 'page', 6, 'page'),
(832, 'parent', 7, 'page', 10, 'page'),
(835, 'matrix', 1, 'question', 18, 'matrix'),
(836, 'matrix', 1, 'question', 19, 'matrix'),
(859, 'lnk_answer', 1, 'comic', 11, 'answer'),
(860, 'lnk_answer', 11, 'answer', 1, 'comic'),
(861, 'lnk_answer', 1, 'comic', 4, 'answer'),
(862, 'lnk_answer', 4, 'answer', 1, 'comic'),
(863, 'lnk_answer', 1, 'comic', 10, 'answer'),
(864, 'lnk_answer', 10, 'answer', 1, 'comic'),
(871, 'lnk_answer', 2, 'comic', 4, 'answer'),
(872, 'lnk_answer', 4, 'answer', 2, 'comic'),
(873, 'lnk_answer', 2, 'comic', 8, 'answer'),
(874, 'lnk_answer', 8, 'answer', 2, 'comic'),
(875, 'test_id', 11, 'question', 4, 'answer'),
(876, 'test_id', 4, 'answer', 11, 'question'),
(877, 'matrix', 11, 'question', 20, 'matrix'),
(878, 'matrix', 20, 'matrix', 11, 'question'),
(879, 'matrix', 11, 'question', 21, 'matrix'),
(880, 'matrix', 21, 'matrix', 11, 'question'),
(891, 'lnk_ingredients', 1, 'rezept', 3, 'ingredient'),
(892, 'lnk_ingredients', 3, 'ingredient', 1, 'rezept'),
(893, 'lnk_ingredients', 1, 'rezept', 1, 'ingredient'),
(894, 'lnk_ingredients', 1, 'ingredient', 1, 'rezept'),
(895, 'lnk_answer', 1, 'rezept', 3, 'answer'),
(896, 'lnk_answer', 3, 'answer', 1, 'rezept'),
(897, 'lnk_answer', 1, 'rezept', 6, 'answer'),
(898, 'lnk_answer', 6, 'answer', 1, 'rezept'),
(925, 'lnk_answer', 1, 'ingredient', 3, 'answer'),
(926, 'lnk_answer', 3, 'answer', 1, 'ingredient');

-- --------------------------------------------------------

--
-- Table structure for table `userpermission`
--

CREATE TABLE `userpermission` (
  `permission_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `permission` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userpermission`
--

INSERT INTO `userpermission` (`permission_id`, `user_id`, `permission`) VALUES
(1, 2, 'question.update');

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `enabled`, `admin`, `password`, `createDate`, `updateDate`, `title`) VALUES
(1, 'Vorname', 'Nachname', 'anubarak1993@gmail.com', 1, 1, '$2y$10$7Bu7/CC1IMHwKGC2Cy8Wxeh996rY.8YdpVa7UastIP5VnHQNPaKkK', '2017-07-19 16:47:08', '2017-07-19 17:34:31', 'Title'),
(4, '0', '0', 'anubarak16@gmail.com', 1, 0, '$2y$10$I8CfLDNws0Ociktmbq5NsOysVeEteVLjOHnLk7zaTrDVOZr1J93sS', '2017-08-03 17:30:27', '2017-08-03 17:30:27', 'Robin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`answer_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `comic`
--
ALTER TABLE `comic`
  ADD PRIMARY KEY (`comic_id`);

--
-- Indexes for table `entrytypes`
--
ALTER TABLE `entrytypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fieldlayout`
--
ALTER TABLE `fieldlayout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fieldlayouttabs`
--
ALTER TABLE `fieldlayouttabs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `folder`
--
ALTER TABLE `folder`
  ADD PRIMARY KEY (`folder_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matrix`
--
ALTER TABLE `matrix`
  ADD PRIMARY KEY (`matrix_id`);

--
-- Indexes for table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`question_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `receipes`
--
ALTER TABLE `receipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relation`
--
ALTER TABLE `relation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userpermission`
--
ALTER TABLE `userpermission`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `comic`
--
ALTER TABLE `comic`
  MODIFY `comic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `entrytypes`
--
ALTER TABLE `entrytypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `fieldlayout`
--
ALTER TABLE `fieldlayout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;
--
-- AUTO_INCREMENT for table `fieldlayouttabs`
--
ALTER TABLE `fieldlayouttabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `folder`
--
ALTER TABLE `folder`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `matrix`
--
ALTER TABLE `matrix`
  MODIFY `matrix_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `receipes`
--
ALTER TABLE `receipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `relation`
--
ALTER TABLE `relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=927;
--
-- AUTO_INCREMENT for table `userpermission`
--
ALTER TABLE `userpermission`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
