-- 24.02.2011 --

ALTER TABLE `mediatypen` DROP PRIMARY KEY

ALTER TABLE `mediatypen` CHANGE `mediatyp` `mediatyp` VARCHAR( 128 ) NOT NULL

ALTER TABLE `mediatypen` ADD `mediaTypID` BIGINT( 128 ) NOT NULL FIRST

ALTER TABLE `mediatypen` ADD PRIMARY KEY ( `mediaTypID` )

ALTER TABLE `mediatypen` CHANGE `mediaTypID` `mediaTypID` BIGINT( 128 ) NOT NULL AUTO_INCREMENT

ALTER TABLE `media` CHANGE `mediatyp` `mediatyp` VARCHAR( 128 ) NOT NULL

UPDATE `wcos`.`media` SET `mediatyp` = 'ANSPRECHPARTNER_BILD' WHERE `media`.`mediaID` = 1; UPDATE `wcos`.`media` SET `mediatyp` = 'FIRMENLOGO' WHERE `media`.`mediaID` = 2; UPDATE `wcos`.`media` SET `mediatyp` = 'LINK' WHERE `media`.`mediaID` = 3; UPDATE `wcos`.`media` SET `mediatyp` = 'FIRMENLOGO' WHERE `media`.`mediaID` = 8; UPDATE `wcos`.`media` SET `mediatyp` = 'BILD' WHERE `media`.`mediaID` = 23; UPDATE `wcos`.`media` SET `mediatyp` = 'BILD' WHERE `media`.`mediaID` = 22; UPDATE `wcos`.`media` SET `mediatyp` = 'FIRMENLOGO' WHERE `media`.`mediaID` = 20; UPDATE `wcos`.`media` SET `mediatyp` = 'BILD' WHERE `media`.`mediaID` = 21; UPDATE `wcos`.`media` SET `mediatyp` = 'LINK' WHERE `media`.`mediaID` = 4; UPDATE `wcos`.`media` SET `mediatyp` = 'FIRMENLOGO' WHERE `media`.`mediaID` = 5; UPDATE `wcos`.`media` SET `mediatyp` = 'BILD' WHERE `media`.`mediaID` = 6; UPDATE `wcos`.`media` SET `mediatyp` = 'LINK' WHERE `media`.`mediaID` = 7;



-- 11.05.2011 --

ALTER TABLE `termine` ADD `loeschenTimer` BIGINT( 128 ) NOT NULL

-- 25.05.2011 --

DROP TRIGGER anbieter_update;
delimiter //
CREATE TRIGGER anbieter_update BEFORE UPDATE ON anbieter
FOR EACH ROW
BEGIN
  IF (SELECT count(*) FROM laufzeiten WHERE anbieterID = OLD.anbieterID) = 0 THEN
    INSERT INTO laufzeiten (anbieterID, startdatum, laufzeit) VALUES (OLD.anbieterID, curdate(), 12);
  END IF;
END; //
delimiter ;

-- 26.05.2011 --

ALTER TABLE `anbieter` ADD `last_login` VARCHAR( 128 ) NOT NULL;

-- 22.07.2011 --

ALTER TABLE `firmenportraits` ADD `firmenbeschreibung` TEXT NOT NULL AFTER `firmenportraitID` 


-- 20.09.2011 --

ALTER TABLE `whitepaper` ADD `whitepaper_datei` VARCHAR( 128 ) NOT NULL 
ALTER TABLE `whitepaper` ADD `whitepaper_datei_originalname` VARCHAR( 128 ) NOT NULL 
ALTER TABLE `whitepaper` ADD `whitepaper_freigabe_hash` VARCHAR( 128 ) NOT NULL 


-- 20.04.2012 --

CREATE TABLE `anbieter` (
  `id` bigint(128) NOT NULL AUTO_INCREMENT,
  `anbieterID` bigint(128) NOT NULL,
  `systemID` varchar(128) NOT NULL DEFAULT '',
  `companyID` bigint(128) NOT NULL,
  `stammdatenID` bigint(128) NOT NULL,
  `firmenname` varchar(128) NOT NULL,
  `anbieterhash` varchar(128) NOT NULL,
  `premiumLevel` int(16) NOT NULL,
  `last_login` varchar(128) NOT NULL,
  `number` varchar(128) DEFAULT NULL,
  `LebenszeitID` varchar(128) DEFAULT NULL,
  `Suchname` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companyID` (`companyID`)
) ENGINE=MyISAM AUTO_INCREMENT=19333 DEFAULT CHARSET=latin1;