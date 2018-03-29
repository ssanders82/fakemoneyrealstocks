-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: fmrs
-- ------------------------------------------------------
-- Server version	5.7.21-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `groupuser`
--

DROP TABLE IF EXISTS `groupuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groupuser` (
  `GroupUserID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `UserEmail` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `AuthCode` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `IsConfirmed` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`GroupUserID`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groupuser`
--

LOCK TABLES `groupuser` WRITE;
/*!40000 ALTER TABLE `groupuser` DISABLE KEYS */;
INSERT INTO `groupuser` VALUES (23,9,'bbillock@zacks.com','1361ba8387a50d0938843555e893d512',0),(21,7,'ssanders82@gmail.com','3c17a5746cf6cf5b3cbd0b33335e4e8f',0),(20,7,'asdf@asdf.com','asdf',0),(19,8,'info@inspectd.com','',1),(18,8,'kevin@t3publishing.com','5ca138ce97a7cdfa8e8bdfe7bb5f8f06',1),(17,8,'dkeohane@hotmail.com','bb5908db5c66bd6aca54362172a321cf',0),(22,9,'johntornatore@gmail.com','',1),(16,8,'kevlund@gmail.com','',1),(12,7,'info@inspectd.com','',1),(13,7,'kevlund@gmail.com','ec43e60be10967814f198bd42ebe2b04',1),(14,7,'djkeohane@hotmail.com','c5d6fe6cca74e94b1206e4cfa3799e9b',1),(15,7,'ssanders82other@gmail.com','a5a8d2a164faf646f2ba1f267856155e',1),(24,9,'rbullock@zacks.com','d815f80687dfe344dfe52e1f43296f18',0),(25,9,'stephenr@zacks.com','9ed83e1700fd87c86ddcc6461cfc1f30',0),(26,9,'kevinm@zacks.com','f41e49f408755144852ffb61ed8af94c',0),(27,9,'jknotts@zacks.com','38b23eff922594c8da91bb33c0172d01',0),(28,9,'crotblut@zacks.com','0fd9b811d56a0cf729b3584bac496b8a',0);
/*!40000 ALTER TABLE `groupuser` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-27 15:33:37
