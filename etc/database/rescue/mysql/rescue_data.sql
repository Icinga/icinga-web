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
-- Dumping data for table `changelog`
--

LOCK TABLES `changelog` WRITE;
/*!40000 ALTER TABLE `changelog` DISABLE KEYS */;
INSERT INTO `changelog` VALUES (1,'Main','2009-12-03 15:25:37','2009-12-03 15:25:37','dbdeploy','1-initial-scheme.sql'),(2,'Main','2009-12-03 15:25:37','2009-12-03 15:25:37','dbdeploy','2-initial-data.sql'),(3,'Main','2009-12-03 15:25:38','2009-12-03 15:25:38','dbdeploy','3-principal-scheme.sql'),(4,'Main','2009-12-03 15:25:38','2009-12-03 15:25:38','dbdeploy','4-principal-data.sql');
/*!40000 ALTER TABLE `changelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_log`
--

LOCK TABLES `nsm_log` WRITE;
/*!40000 ALTER TABLE `nsm_log` DISABLE KEYS */;
INSERT INTO `nsm_log` VALUES (1,8,'User root (Root, Enoch) logged in!','2009-12-04 11:26:44','2009-12-04 11:26:44');
/*!40000 ALTER TABLE `nsm_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_principal`
--

LOCK TABLES `nsm_principal` WRITE;
/*!40000 ALTER TABLE `nsm_principal` DISABLE KEYS */;
INSERT INTO `nsm_principal` VALUES (1,1,NULL,'user',0),(2,NULL,3,'role',0),(3,NULL,4,'role',0),(4,NULL,5,'role',0);
/*!40000 ALTER TABLE `nsm_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_principal_target`
--

LOCK TABLES `nsm_principal_target` WRITE;
/*!40000 ALTER TABLE `nsm_principal_target` DISABLE KEYS */;
/*!40000 ALTER TABLE `nsm_principal_target` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_role`
--

LOCK TABLES `nsm_role` WRITE;
/*!40000 ALTER TABLE `nsm_role` DISABLE KEYS */;
INSERT INTO `nsm_role` VALUES (3,'appkit_user','Appkit user',0,'2009-02-19 09:17:37','2009-04-20 16:12:17'),(4,'appkit_admin','AppKit admin',0,'2009-02-17 10:17:10','0000-00-00 00:00:00'),(5,'icinga_user','The default representation of a ICINGA user',0,'2009-04-20 15:56:52','2009-04-20 15:56:52');
/*!40000 ALTER TABLE `nsm_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_target`
--

LOCK TABLES `nsm_target` WRITE;
/*!40000 ALTER TABLE `nsm_target` DISABLE KEYS */;
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
INSERT INTO `nsm_user` VALUES (1,0,'root','Root','Enoch','42bc5093863dce8c150387a5bb7e3061cf3ea67d2cf1779671e1b0f435e953a1','0c099ae4627b144f3a7eaa763ba43b10fd5d1caa8738a98f11bb973bebc52ccd','root@localhost.local',0,'2009-02-18 10:12:59','2009-02-18 10:12:59');
/*!40000 ALTER TABLE `nsm_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_user_preference`
--

LOCK TABLES `nsm_user_preference` WRITE;
/*!40000 ALTER TABLE `nsm_user_preference` DISABLE KEYS */;
/*!40000 ALTER TABLE `nsm_user_preference` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `nsm_user_role`
--

LOCK TABLES `nsm_user_role` WRITE;
/*!40000 ALTER TABLE `nsm_user_role` DISABLE KEYS */;
INSERT INTO `nsm_user_role` VALUES (1,4),(1,5);
/*!40000 ALTER TABLE `nsm_user_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-12-04 12:13:16
