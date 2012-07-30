
/*CREATE DATABASE `crawldb`*/


CREATE TABLE `tblDomains` (
  `iDomainID` int(11) NOT NULL AUTO_INCREMENT,
  `strDomain` varchar(128) DEFAULT NULL,
  `dtLastAccessed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iDomainID`),
  UNIQUE KEY `strDomain_UNIQUE` (`strDomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tblLinks` (
  `iLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `fkParentID` int(11) NOT NULL,
  `fkQueryID` int(11) NOT NULL,
  `fkChildID` int(11) NOT NULL,
  `iNumberTimes` int(11) NOT NULL,
  `iLevel` int(11) NOT NULL,
  `bolExclude` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`iLinkID`),
  KEY `fkParentID` (`fkParentID`),
  KEY `fkChildID` (`fkChildID`),
  KEY `Joint` (`fkParentID`,`fkChildID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblPages` (
  `iPageID` int(11) NOT NULL AUTO_INCREMENT,
  `strURL` varchar(255) NOT NULL,
  `strDomain` varchar(255) DEFAULT NULL,
  `strCleanURL` varchar(255) DEFAULT NULL,
  `iLevel` int(11) NOT NULL DEFAULT 0,
/*  `fkDomainID` int(11) NOT NULL,*/
  `fkQueryID` int(11) DEFAULT 0,
  `strHTML` longtext,
  `strHeader` text,
  `dtDays` int(11) DEFAULT NULL,
  `bolProcessed` tinyint(1) NOT NULL DEFAULT '0',
  `bolHarvested` tinyint(1) NOT NULL DEFAULT '0',
  `bolExclude` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`iPageID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `tblConfig` (
  `strName` varchar(50) NOT NULL,
  `strValue` varchar(50) NOT NULL,
  PRIMARY KEY (`strName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO tblConfig (strName,strValue) VALUES ('CrawlerStatus','OK');
/*Use 'CrawlerStatus','STOP' to  have crawler stop gracefully at the end of parsing the current page*/


