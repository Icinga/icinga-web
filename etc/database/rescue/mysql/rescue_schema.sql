-- MySQL dump 10.11
--
-- Host: localhost    Database: icinga_web
-- ------------------------------------------------------
-- Server version	5.1.40
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `changelog`
--

DROP TABLE IF EXISTS `changelog`;
CREATE TABLE `changelog` (
  `change_number` bigint(20) NOT NULL,
  `delta_set` varchar(10) NOT NULL,
  `start_dt` timestamp NOT NULL,
  `complete_dt` timestamp NULL DEFAULT NULL,
  `applied_by` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`change_number`,`delta_set`)
) TYPE=InnoDB;

--
-- Table structure for table `nsm_log`
--

DROP TABLE IF EXISTS `nsm_log`;
CREATE TABLE `nsm_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_level` int(10) NOT NULL,
  `log_message` varchar(4000) NOT NULL,
  `log_created` datetime NOT NULL,
  `log_modified` datetime NOT NULL,
  PRIMARY KEY (`log_id`)
) TYPE=InnoDB AUTO_INCREMENT=2;

--
-- Table structure for table `nsm_principal`
--

DROP TABLE IF EXISTS `nsm_principal`;
CREATE TABLE `nsm_principal` (
  `principal_id` int(11) NOT NULL AUTO_INCREMENT,
  `principal_user_id` int(10) DEFAULT NULL,
  `principal_role_id` int(10) DEFAULT NULL,
  `principal_type` enum('role','user') NOT NULL,
  `principal_disabled` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`principal_id`),
  KEY `fk_nsm_principle_nsm_user1` (`principal_user_id`),
  KEY `fk_nsm_principle_nsm_role1` (`principal_role_id`),
  CONSTRAINT `fk_nsm_principle_nsm_role1` FOREIGN KEY (`principal_role_id`) REFERENCES `nsm_role` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_nsm_principle_nsm_user1` FOREIGN KEY (`principal_user_id`) REFERENCES `nsm_user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) TYPE=InnoDB AUTO_INCREMENT=5;

--
-- Table structure for table `nsm_principal_target`
--

DROP TABLE IF EXISTS `nsm_principal_target`;
CREATE TABLE `nsm_principal_target` (
  `pt_id` int(11) NOT NULL AUTO_INCREMENT,
  `pt_principal_id` int(11) NOT NULL,
  `pt_target_id` int(11) NOT NULL,
  PRIMARY KEY (`pt_id`),
  KEY `fk_nsm_principal_has_nsm_target_nsm_principal1` (`pt_principal_id`),
  KEY `fk_nsm_principal_has_nsm_target_nsm_target1` (`pt_target_id`),
  CONSTRAINT `fk_nsm_principal_has_nsm_target_nsm_principal1` FOREIGN KEY (`pt_principal_id`) REFERENCES `nsm_principal` (`principal_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_nsm_principal_has_nsm_target_nsm_target1` FOREIGN KEY (`pt_target_id`) REFERENCES `nsm_target` (`target_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) TYPE=InnoDB;

--
-- Table structure for table `nsm_role`
--

DROP TABLE IF EXISTS `nsm_role`;
CREATE TABLE `nsm_role` (
  `role_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(40) NOT NULL,
  `role_description` varchar(255) DEFAULT NULL,
  `role_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `role_created` datetime NOT NULL,
  `role_modified` datetime NOT NULL,
  PRIMARY KEY (`role_id`)
) TYPE=InnoDB AUTO_INCREMENT=6;

--
-- Table structure for table `nsm_session`
--

DROP TABLE IF EXISTS `nsm_session`;
CREATE TABLE `nsm_session` (
  `session_entry_id` int(10) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `session_data` longblob NOT NULL,
  `session_checksum` varchar(255) NOT NULL,
  `session_created` datetime NOT NULL,
  `session_modified` datetime NOT NULL,
  PRIMARY KEY (`session_entry_id`)
) TYPE=InnoDB AUTO_INCREMENT=132;

--
-- Table structure for table `nsm_target`
--

DROP TABLE IF EXISTS `nsm_target`;
CREATE TABLE `nsm_target` (
  `target_id` int(11) NOT NULL,
  `target_name` varchar(45) DEFAULT NULL,
  `target_description` varchar(100) DEFAULT NULL,
  `target_class` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`target_id`)
) TYPE=InnoDB;

--
-- Table structure for table `nsm_target_value`
--

DROP TABLE IF EXISTS `nsm_target_value`;
CREATE TABLE `nsm_target_value` (
  `tv_pt_id` int(11) NOT NULL,
  `tv_key` varchar(45) NOT NULL,
  `tv_val` varchar(45) NOT NULL,
  PRIMARY KEY (`tv_pt_id`,`tv_key`),
  KEY `fk_nsm_target_value_nsm_principal_target1` (`tv_pt_id`),
  CONSTRAINT `fk_nsm_target_value_nsm_principal_target1` FOREIGN KEY (`tv_pt_id`) REFERENCES `nsm_principal_target` (`pt_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) TYPE=InnoDB;

--
-- Table structure for table `nsm_user`
--

DROP TABLE IF EXISTS `nsm_user`;
CREATE TABLE `nsm_user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_account` int(40) NOT NULL,
  `user_name` varchar(18) NOT NULL,
  `user_lastname` varchar(40) NOT NULL,
  `user_firstname` varchar(40) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `user_salt` varchar(64) NOT NULL,
  `user_email` varchar(40) NOT NULL,
  `user_disabled` tinyint(1) NOT NULL DEFAULT '1',
  `user_created` datetime NOT NULL,
  `user_modified` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nsm_user_name_idx` (`user_name`)
) TYPE=InnoDB AUTO_INCREMENT=2;

--
-- Table structure for table `nsm_user_preference`
--

DROP TABLE IF EXISTS `nsm_user_preference`;
CREATE TABLE `nsm_user_preference` (
  `upref_id` int(10) NOT NULL AUTO_INCREMENT,
  `upref_user_id` int(10) NOT NULL,
  `upref_val` varchar(100) DEFAULT NULL,
  `upref_longval` blob,
  `upref_key` varchar(50) NOT NULL,
  `upref_created` datetime NOT NULL,
  `upref_modified` datetime NOT NULL,
  PRIMARY KEY (`upref_id`),
  UNIQUE KEY `nsm_user_preference_userkey` (`upref_user_id`,`upref_key`),
  CONSTRAINT `nsm_user_nsm_user_preference_fk` FOREIGN KEY (`upref_user_id`) REFERENCES `nsm_user` (`user_id`)
) TYPE=InnoDB;

--
-- Table structure for table `nsm_user_role`
--

DROP TABLE IF EXISTS `nsm_user_role`;
CREATE TABLE `nsm_user_role` (
  `usro_user_id` int(10) NOT NULL,
  `usro_role_id` int(10) NOT NULL,
  PRIMARY KEY (`usro_user_id`,`usro_role_id`),
  KEY `nsm_usro_role_fk` (`usro_role_id`),
  CONSTRAINT `nsm_usro_role_fk` FOREIGN KEY (`usro_role_id`) REFERENCES `nsm_role` (`role_id`),
  CONSTRAINT `nsm_usro_user_fk` FOREIGN KEY (`usro_user_id`) REFERENCES `nsm_user` (`user_id`)
) TYPE=InnoDB;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-12-04 12:11:35
