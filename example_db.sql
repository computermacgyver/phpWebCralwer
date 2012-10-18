
/*CREATE DATABASE `crawldb`*/

/*IMPORTANT: 
 * Please adjust the MAX_ALLOWED_PACKET option on the server to at least 5M or whatever the size of the largest webpage you wish to store is.
 * Default for MAX_ALLOWED_PACKET is 1M, which will cause a failure in this application for any webpages larger than 1MB.
 * MAX_ALLOWED_PACKET is set in the server my.cnf file.
 * http://dev.mysql.com/doc/refman/5.0/en/packet-too-large.html
 * http://dev.mysql.com/doc/refman/5.0/en/server-system-variables.html#sysvar_max_allowed_packet
*/ 


CREATE TABLE `tblDomains` (
  `iDomainID` int(11) NOT NULL AUTO_INCREMENT,
  `strDomain` varchar(2048) DEFAULT NULL,
  `dtLastAccessed` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iDomainID`)
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
  `strURL` varchar(2048) NOT NULL,
  `strDomain` varchar(2048) DEFAULT NULL,
  `strCleanURL` varchar(2048) DEFAULT NULL,
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

CREATE TABLE `tblExternalHosts` (
  `iHostID` int(11) NOT NULL AUTO_INCREMENT,
  `strFromDomain` varchar(2048) DEFAULT NULL,
  `strToDomain` varchar(2048) DEFAULT NULL,
  `iCount` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`iHostID`)
) ENGINE=InnoDB AUTO_INCREMENT=166125 DEFAULT CHARSET=utf8;




