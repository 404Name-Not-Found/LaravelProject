/*
MySQL Backup
Database: csc364chms
Backup Time: 2026-09-28 09:44:58
*/

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `csc364chms`.`contacts`;
DROP TABLE IF EXISTS `csc364chms`.`donations`;
DROP TABLE IF EXISTS `csc364chms`.`event_participants`;
DROP TABLE IF EXISTS `csc364chms`.`events`;
DROP TABLE IF EXISTS `csc364chms`.`forms`;
DROP TABLE IF EXISTS `csc364chms`.`households`;
DROP TABLE IF EXISTS `csc364chms`.`locations408`;
CREATE TABLE `contacts` (
  `Contact_ID` int NOT NULL AUTO_INCREMENT,
  `Last_Name` varchar(128) NOT NULL,
  `First_Name` varchar(128) NOT NULL,
  `Date_of_Birth` datetime DEFAULT NULL,
  `Gender` varchar(16) DEFAULT NULL,
  `Marital_Status` varchar(16) DEFAULT NULL,
  `Contact_Status` varchar(16) DEFAULT NULL,
  `Household_ID` int DEFAULT NULL,
  `Household_Position` varchar(48) DEFAULT NULL,
  `Donor_ID` int DEFAULT NULL,
  `Email_address` varchar(255) DEFAULT NULL,
  `Mobile_Phone` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`Contact_ID`),
  UNIQUE KEY `Donor_ID` (`Donor_ID`),
  KEY `FK_Households_Contacts` (`Household_ID`),
  CONSTRAINT `FK_Households_Contacts` FOREIGN KEY (`Household_ID`) REFERENCES `households` (`Household_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=34971 DEFAULT CHARSET=latin1;
CREATE TABLE `donations` (
  `Donation_ID` int NOT NULL AUTO_INCREMENT,
  `Donor_ID` int DEFAULT NULL,
  `Donation_Amount` float DEFAULT NULL,
  `Donation_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`Donation_ID`),
  KEY `FK_Donations_Donor` (`Donor_ID`),
  CONSTRAINT `FK_Donations_Donor` FOREIGN KEY (`Donor_ID`) REFERENCES `contacts` (`Donor_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=711727 DEFAULT CHARSET=latin1;
CREATE TABLE `event_participants` (
  `Event_Participant_ID` int NOT NULL AUTO_INCREMENT,
  `Event_ID` int DEFAULT NULL,
  `Contact_ID` int DEFAULT NULL,
  PRIMARY KEY (`Event_Participant_ID`),
  KEY `FK_Contacts_EP` (`Contact_ID`),
  KEY `FK_Events_EP` (`Event_ID`),
  CONSTRAINT `FK_Contacts_EP` FOREIGN KEY (`Contact_ID`) REFERENCES `contacts` (`Contact_ID`),
  CONSTRAINT `FK_Events_EP` FOREIGN KEY (`Event_ID`) REFERENCES `events` (`Event_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=179027 DEFAULT CHARSET=latin1;
CREATE TABLE `events` (
  `Event_ID` int NOT NULL AUTO_INCREMENT,
  `Event_Title` varchar(256) DEFAULT NULL,
  `Event_Description` varchar(4096) DEFAULT NULL,
  `Event_Start_Date` datetime DEFAULT NULL,
  `Event_End_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`Event_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=32443 DEFAULT CHARSET=latin1;
CREATE TABLE `forms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `class` varchar(32) DEFAULT NULL,
  `termsAndConditions` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
CREATE TABLE `households` (
  `Household_ID` int NOT NULL AUTO_INCREMENT,
  `Household_Name` varchar(255) DEFAULT NULL,
  `Address_Line_1` varchar(255) DEFAULT NULL,
  `City` varchar(255) DEFAULT NULL,
  `State` varchar(8) DEFAULT NULL,
  `Zip_Code` varchar(16) DEFAULT NULL,
  `Home_Phone` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`Household_ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30600 DEFAULT CHARSET=latin1;
CREATE TABLE `locations408` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `permanent` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;