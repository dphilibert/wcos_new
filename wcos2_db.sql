-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 10. Jun 2013 um 13:06
-- Server Version: 5.1.63-0ubuntu0.11.10.1
-- PHP-Version: 5.3.6-13ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `wcos2`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `anbieter`
--

CREATE TABLE IF NOT EXISTS `anbieter` (
  `id` bigint(128) NOT NULL AUTO_INCREMENT,
  `anbieterID` bigint(128) NOT NULL,
  `stammdatenID` bigint(128) NOT NULL,
  `firmenname` varchar(128) NOT NULL,
  `name1` varchar(128) DEFAULT NULL,
  `name2` varchar(128) DEFAULT NULL,
  `anbieterhash` varchar(128) NOT NULL,
  `last_login` varchar(128) NOT NULL,
  `number` varchar(128) DEFAULT NULL,
  `LebenszeitID` varchar(128) DEFAULT NULL,
  `Suchname` varchar(128) DEFAULT NULL,
  `lastChange` varchar(128) DEFAULT NULL,
  `created` varchar(128) DEFAULT NULL,
  `visits` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `firmenname` (`firmenname`),
  KEY `anbieterID` (`anbieterID`),
  KEY `number` (`number`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20094 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ansprechpartner`
--

CREATE TABLE IF NOT EXISTS `ansprechpartner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anbieterID` bigint(128) NOT NULL,
  `vorname` varchar(128) NOT NULL,
  `nachname` varchar(128) NOT NULL,
  `abteilung` varchar(128) NOT NULL,
  `position` varchar(128) NOT NULL,
  `telefon` varchar(128) NOT NULL,
  `telefax` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `system_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=171 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `anbieterID` int(11) DEFAULT NULL,
  `system_id` int(11) DEFAULT NULL,
  `module` varchar(128) DEFAULT NULL,
  `action` varchar(128) DEFAULT NULL,
  `object_id` varchar(128) NOT NULL,
  `tstamp` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=126 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anbieterID` bigint(128) NOT NULL,
  `media_type` int(11) DEFAULT NULL,
  `beschreibung` text NOT NULL,
  `media` text,
  `link` text,
  `object_id` int(11) DEFAULT NULL,
  `system_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=257 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `product2provider`
--

CREATE TABLE IF NOT EXISTS `product2provider` (
  `product` int(11) DEFAULT NULL,
  `anbieterID` int(11) DEFAULT NULL, 
  KEY `produktcode` (`product`,`anbieterID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `haupt` varchar(255) DEFAULT NULL,
  `ober` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  `system_id` int(16) DEFAULT NULL,
  KEY `hauptbegriff` (`haupt`,`ober`,`name`,`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anbieterID` int(11) DEFAULT NULL,
  `value` text,
  `type` int(11) DEFAULT NULL,
  `system_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=178 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stammdaten`
--

CREATE TABLE IF NOT EXISTS `stammdaten` (
  `id` bigint(128) NOT NULL AUTO_INCREMENT,
  `anbieterID` bigint(128) DEFAULT NULL,
  `strasse` varchar(128) NOT NULL,
  `hausnummer` varchar(32) NOT NULL,
  `land` varchar(16) NOT NULL,
  `plz` varchar(16) NOT NULL,
  `ort` varchar(128) NOT NULL,
  `fon` varchar(64) NOT NULL,
  `fax` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `www` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hausnummer` (`hausnummer`),
  KEY `land` (`land`),
  KEY `plz` (`plz`),
  KEY `ort` (`ort`),
  KEY `fon` (`fon`),
  KEY `fax` (`fax`),
  KEY `email` (`email`),
  KEY `strasse` (`strasse`),
  FULLTEXT KEY `www` (`www`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=760114723 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `systeme`
--

CREATE TABLE IF NOT EXISTS `systeme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anbieterID` int(11) DEFAULT NULL,
  `system_id` int(11) DEFAULT NULL,
  `premium` int(11) DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9499 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termine`
--

CREATE TABLE IF NOT EXISTS `termine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anbieterID` bigint(128) NOT NULL,
  `title` text,
  `teaser` text NOT NULL,
  `beschreibung` text NOT NULL,
  `typID` bigint(128) NOT NULL,
  `beginn` varchar(32) NOT NULL,
  `ende` varchar(32) NOT NULL,
  `ort` varchar(128) NOT NULL,
  `system_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=200 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `whitepaper`
--

CREATE TABLE IF NOT EXISTS `whitepaper` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anbieterID` int(11) DEFAULT NULL,
  `link` text,
  `beschreibung` text,
  `title` text,
  `system_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;