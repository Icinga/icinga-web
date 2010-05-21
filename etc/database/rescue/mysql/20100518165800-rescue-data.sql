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
-- Dumping data for table `migration_version`
--

LOCK TABLES `migration_version` WRITE;
/*!40000 ALTER TABLE `migration_version` DISABLE KEYS */;
INSERT INTO `migration_version` VALUES (11);
/*!40000 ALTER TABLE `migration_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_log`
--

LOCK TABLES `nsm_log` WRITE;
/*!40000 ALTER TABLE `nsm_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `nsm_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_principal`
--

LOCK TABLES `nsm_principal` WRITE;
/*!40000 ALTER TABLE `nsm_principal` DISABLE KEYS */;
INSERT INTO `nsm_principal` VALUES (1,1,NULL,'user',0),(2,NULL,2,'role',0),(3,NULL,3,'role',0),(4,NULL,1,'role',0),(5,NULL,4,'role',0);
/*!40000 ALTER TABLE `nsm_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_principal_target`
--

LOCK TABLES `nsm_principal_target` WRITE;
/*!40000 ALTER TABLE `nsm_principal_target` DISABLE KEYS */;
INSERT INTO `nsm_principal_target` VALUES (1,2,8),(2,3,9),(3,3,10),(4,3,11),(5,4,8),(6,5,7);
/*!40000 ALTER TABLE `nsm_principal_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_role`
--

LOCK TABLES `nsm_role` WRITE;
/*!40000 ALTER TABLE `nsm_role` DISABLE KEYS */;
INSERT INTO `nsm_role` VALUES (1,'icinga_user','The default representation of a ICINGA user',0,'2009-04-20 15:56:52','2010-05-11 13:09:20',NULL),(2,'appkit_user','Appkit user test',0,'2009-02-19 09:17:37','2010-05-11 13:09:13',NULL),(3,'appkit_admin','AppKit admin',0,'2009-02-17 10:17:10','2010-05-11 13:08:55',NULL),(4,'guest','Unauthorized Guest',0,'2009-02-17 10:17:10','2010-05-11 13:09:26',NULL);
/*!40000 ALTER TABLE `nsm_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_session`
--

LOCK TABLES `nsm_session` WRITE;
/*!40000 ALTER TABLE `nsm_session` DISABLE KEYS */;
INSERT INTO `nsm_session` VALUES (1,'c0lqjsulm91r3dg4539jb0khj2','ICINGAAppKit','org.agavi.user.RbacSecurityUser.roles|a:0:{}org.agavi.user.BasicSecurityUser.authenticated|b:0;org.agavi.user.BasicSecurityUser.credentials|a:0:{}org.agavi.user.User|a:0:{}','a2a23282960871a80d7aa00f39a69bb0','2010-05-18 14:55:14','2010-05-18 14:55:14');
/*!40000 ALTER TABLE `nsm_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_target`
--

LOCK TABLES `nsm_target` WRITE;
/*!40000 ALTER TABLE `nsm_target` DISABLE KEYS */;
INSERT INTO `nsm_target` VALUES (1,'IcingaHostgroup','Limit data access to specific hostgroups','IcingaDataHostgroupPrincipalTarget','icinga'),(2,'IcingaServicegroup','Limit data access to specific servicegroups','IcingaDataServicegroupPrincipalTarget','icinga'),(3,'IcingaHostCustomVariablePair','Limit data access to specific custom variables','IcingaDataHostCustomVariablePrincipalTarget','icinga'),(4,'IcingaServiceCustomVariablePair','Limit data access to specific custom variables','IcingaDataServiceCustomVariablePrincipalTarget','icinga'),(5,'IcingaContactgroup','Limit data access to users contact group membership','IcingaDataContactgroupPrincipalTarget','icinga'),(6,'IcingaCommandRo','Limit access to commands','IcingaDataCommandRoPrincipalTarget','icinga'),(7,'appkit.access','Access to login-page (which, actually, means no access)','','credential'),(8,'icinga.user','Access to icinga','','credential'),(9,'appkit.admin.groups','Access to group editor','','credential'),(10,'appkit.admin.users','Access to user editor','','credential'),(11,'appkit.admin','Access to admin panel ','','credential'),(12,'appkit.user.dummy','Basic right for users','','credential'),(13,'module.heatmap','Allow access to the heatmap module','','credential'),(14,'module.heatmap.room.create','Allow creation of heatmaps','','credential'),(15,'module.heatmap.schedules','Allow management of schedules','','credential');
/*!40000 ALTER TABLE `nsm_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_target_value`
--

LOCK TABLES `nsm_target_value` WRITE;
/*!40000 ALTER TABLE `nsm_target_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `nsm_target_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_user`
--

LOCK TABLES `nsm_user` WRITE;
/*!40000 ALTER TABLE `nsm_user` DISABLE KEYS */;
INSERT INTO `nsm_user` VALUES (1,0,'root','Root','Enoch','18242a1cc143f34faf45e0f08a962bd892ce58fb3aa8af3392da9149838c73d9','5136137213e7403acdbc564a3203c721cea1ea9d4c28b3722da104955861976f','internal','',NULL,'root@localhost.local',0,'2009-02-18 10:12:59','2010-05-11 13:10:16');
/*!40000 ALTER TABLE `nsm_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_user_preference`
--

LOCK TABLES `nsm_user_preference` WRITE;
/*!40000 ALTER TABLE `nsm_user_preference` DISABLE KEYS */;
INSERT INTO `nsm_user_preference` VALUES (1,1,NULL,'[]','de.icinga.ext.appstate','2010-05-11 13:07:58','2010-05-11 14:04:27');
/*!40000 ALTER TABLE `nsm_user_preference` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_user_role`
--

LOCK TABLES `nsm_user_role` WRITE;
/*!40000 ALTER TABLE `nsm_user_role` DISABLE KEYS */;
INSERT INTO `nsm_user_role` VALUES (1,1),(1,2),(1,3);
/*!40000 ALTER TABLE `nsm_user_role` ENABLE KEYS */;
UNLOCK TABLES;

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
