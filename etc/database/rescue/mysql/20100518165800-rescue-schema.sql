-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: icinga_web
-- ------------------------------------------------------
-- Server version	5.5.3-m3-log
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `migration_version`
--

DROP TABLE IF EXISTS `migration_version`;
CREATE TABLE `migration_version` (
  `version` int(11) DEFAULT NULL
) TYPE=InnoDB;

--
-- Table structure for table `nsm_log`
--

DROP TABLE IF EXISTS `nsm_log`;
CREATE TABLE `nsm_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_level` int(11) NOT NULL,
  `log_message` text NOT NULL,
  `log_created` datetime NOT NULL,
  `log_modified` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
) TYPE=InnoDB;

--
-- Table structure for table `nsm_principal`
--

DROP TABLE IF EXISTS `nsm_principal`;
CREATE TABLE `nsm_principal` (
  `principal_id` int(11) NOT NULL AUTO_INCREMENT,
  `principal_user_id` int(11) DEFAULT NULL,
  `principal_role_id` int(11) DEFAULT NULL,
  `principal_type` varchar(4) NOT NULL,
  `principal_disabled` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`principal_id`),
  KEY `nsm_principal_principal_user_id_nsm_user_user_id` (`principal_user_id`),
  KEY `nsm_principal_principal_role_id_nsm_role_role_id` (`principal_role_id`),
  CONSTRAINT `nsm_principal_principal_role_id_nsm_role_role_id` FOREIGN KEY (`principal_role_id`) REFERENCES `nsm_role` (`role_id`),
  CONSTRAINT `nsm_principal_principal_user_id_nsm_user_user_id` FOREIGN KEY (`principal_user_id`) REFERENCES `nsm_user` (`user_id`)
) TYPE=InnoDB AUTO_INCREMENT=6;

--
-- Table structure for table `nsm_principal_target`
--

DROP TABLE IF EXISTS `nsm_principal_target`;
CREATE TABLE `nsm_principal_target` (
  `pt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_principal_id` int(11) NOT NULL,
  `pt_target_id` int(11) NOT NULL,
  PRIMARY KEY (`pt_id`),
  KEY `nsm_principal_target_pt_target_id_nsm_target_target_id` (`pt_target_id`),
  KEY `nsm_principal_target_pt_principal_id_nsm_principal_principal_id` (`pt_principal_id`),
  CONSTRAINT `nsm_principal_target_pt_principal_id_nsm_principal_principal_id` FOREIGN KEY (`pt_principal_id`) REFERENCES `nsm_principal` (`principal_id`),
  CONSTRAINT `nsm_principal_target_pt_target_id_nsm_target_target_id` FOREIGN KEY (`pt_target_id`) REFERENCES `nsm_target` (`target_id`)
) TYPE=InnoDB AUTO_INCREMENT=7;

--
-- Table structure for table `nsm_role`
--

DROP TABLE IF EXISTS `nsm_role`;
CREATE TABLE `nsm_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(40) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  `role_disabled` tinyint(4) NOT NULL DEFAULT '0',
  `role_created` datetime NOT NULL,
  `role_modified` datetime NOT NULL,
  `role_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) TYPE=InnoDB AUTO_INCREMENT=5;

--
-- Table structure for table `nsm_session`
--

DROP TABLE IF EXISTS `nsm_session`;
CREATE TABLE `nsm_session` (
  `session_entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `session_data` longblob NOT NULL,
  `session_checksum` varchar(255) NOT NULL,
  `session_created` datetime NOT NULL,
  `session_modified` datetime NOT NULL,
  PRIMARY KEY (`session_entry_id`)
) TYPE=InnoDB AUTO_INCREMENT=2;

--
-- Table structure for table `nsm_target`
--

DROP TABLE IF EXISTS `nsm_target`;
CREATE TABLE `nsm_target` (
  `target_id` int(11) NOT NULL AUTO_INCREMENT,
  `target_name` varchar(45) NOT NULL,
  `target_description` varchar(100) DEFAULT NULL,
  `target_class` varchar(80) NOT NULL,
  `target_type` varchar(45) NOT NULL,
  PRIMARY KEY (`target_id`)
) TYPE=InnoDB AUTO_INCREMENT=16;

--
-- Table structure for table `nsm_target_value`
--

DROP TABLE IF EXISTS `nsm_target_value`;
CREATE TABLE `nsm_target_value` (
  `tv_pt_id` int(11) NOT NULL DEFAULT '0',
  `tv_key` varchar(45) NOT NULL DEFAULT '',
  `tv_val` varchar(45) NOT NULL,
  PRIMARY KEY (`tv_pt_id`,`tv_key`),
  CONSTRAINT `nsm_target_value_tv_pt_id_nsm_principal_target_pt_id` FOREIGN KEY (`tv_pt_id`) REFERENCES `nsm_principal_target` (`pt_id`)
) TYPE=InnoDB;

--
-- Table structure for table `nsm_user`
--

DROP TABLE IF EXISTS `nsm_user`;
CREATE TABLE `nsm_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_account` int(11) NOT NULL,
  `user_name` varchar(18) NOT NULL,
  `user_lastname` varchar(40) NOT NULL,
  `user_firstname` varchar(40) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_salt` varchar(64) NOT NULL,
  `user_authsrc` varchar(45) NOT NULL,
  `user_authid` varchar(127) DEFAULT NULL,
  `user_authkey` varchar(64) DEFAULT NULL,
  `user_email` varchar(40) NOT NULL,
  `user_disabled` tinyint(4) NOT NULL DEFAULT '1',
  `user_created` datetime NOT NULL,
  `user_modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_unique_idx` (`user_name`),
  KEY `user_search_idx` (`user_name`,`user_authsrc`,`user_authid`,`user_disabled`)
) TYPE=InnoDB AUTO_INCREMENT=2;

--
-- Table structure for table `nsm_user_preference`
--

DROP TABLE IF EXISTS `nsm_user_preference`;
CREATE TABLE `nsm_user_preference` (
  `upref_id` int(11) NOT NULL AUTO_INCREMENT,
  `upref_user_id` int(11) NOT NULL,
  `upref_val` varchar(100) DEFAULT NULL,
  `upref_longval` longblob,
  `upref_key` varchar(50) NOT NULL,
  `upref_created` datetime NOT NULL,
  `upref_modified` datetime NOT NULL,
  PRIMARY KEY (`upref_id`),
  KEY `upref_search_key_idx` (`upref_key`),
  KEY `nsm_user_preference_upref_user_id_nsm_user_user_id` (`upref_user_id`),
  CONSTRAINT `nsm_user_preference_upref_user_id_nsm_user_user_id` FOREIGN KEY (`upref_user_id`) REFERENCES `nsm_user` (`user_id`)
) TYPE=InnoDB AUTO_INCREMENT=2;

--
-- Table structure for table `nsm_user_role`
--

DROP TABLE IF EXISTS `nsm_user_role`;
CREATE TABLE `nsm_user_role` (
  `usro_user_id` int(11) NOT NULL DEFAULT '0',
  `usro_role_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usro_user_id`,`usro_role_id`),
  KEY `nsm_user_role_usro_role_id_nsm_role_role_id` (`usro_role_id`),
  CONSTRAINT `nsm_user_role_usro_role_id_nsm_role_role_id` FOREIGN KEY (`usro_role_id`) REFERENCES `nsm_role` (`role_id`),
  CONSTRAINT `nsm_user_role_usro_user_id_nsm_user_user_id` FOREIGN KEY (`usro_user_id`) REFERENCES `nsm_user` (`user_id`)
) TYPE=InnoDB;

--
-- Dumping events for database 'icinga_web'
--

--
-- Dumping routines for database 'icinga_web'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-18 16:58:00
