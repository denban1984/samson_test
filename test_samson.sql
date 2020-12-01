-- MySQL dump 10.13  Distrib 5.7.31, for Win64 (x86_64)
--
-- Host: localhost    Database: test_samson
-- ------------------------------------------------------
-- Server version	5.7.31-log

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
-- Table structure for table `a_category`
--

DROP TABLE IF EXISTS `a_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_category` (
  `CODE_RUBRIC` int(9) NOT NULL AUTO_INCREMENT,
  `NAME_RUBRIC` varchar(255) NOT NULL,
  PRIMARY KEY (`CODE_RUBRIC`),
  UNIQUE KEY `NAME_RUBRIC` (`NAME_RUBRIC`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_category`
--

LOCK TABLES `a_category` WRITE;
/*!40000 ALTER TABLE `a_category` DISABLE KEYS */;
INSERT INTO `a_category` VALUES (9,'ÐŸÑ€Ð¸Ð½Ñ‚ÐµÑ€Ñ‹'),(10,'ÐœÐ¤Ð£'),(7,'Ð‘ÑƒÐ¼Ð°Ð³Ð°');
/*!40000 ALTER TABLE `a_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_category_chain`
--

DROP TABLE IF EXISTS `a_category_chain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_category_chain` (
  `CODE_PARENT_RUBRIC` int(9) NOT NULL,
  `CODE_RUBRIC` int(9) NOT NULL,
  KEY `CODE_RUBRIC` (`CODE_RUBRIC`),
  KEY `CODE_PARENT_RUBRIC` (`CODE_PARENT_RUBRIC`),
  CONSTRAINT `a_category_chain_ibfk_1` FOREIGN KEY (`CODE_RUBRIC`) REFERENCES `a_category` (`CODE_RUBRIC`) ON DELETE CASCADE,
  CONSTRAINT `a_category_chain_ibfk_2` FOREIGN KEY (`CODE_PARENT_RUBRIC`) REFERENCES `a_category` (`CODE_RUBRIC`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_category_chain`
--

LOCK TABLES `a_category_chain` WRITE;
/*!40000 ALTER TABLE `a_category_chain` DISABLE KEYS */;
/*!40000 ALTER TABLE `a_category_chain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_price`
--

DROP TABLE IF EXISTS `a_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_price` (
  `ID` int(9) NOT NULL AUTO_INCREMENT,
  `PRODUCT_CODE` int(9) NOT NULL,
  `PRICE_TYPE_ID` int(9) NOT NULL,
  `PRICE` decimal(19,2) NOT NULL,
  `LAST_MODIFY` datetime NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `PRODUCT_CODE` (`PRODUCT_CODE`),
  KEY `PRICE_TYPE_ID` (`PRICE_TYPE_ID`),
  CONSTRAINT `a_price_ibfk_1` FOREIGN KEY (`PRODUCT_CODE`) REFERENCES `a_product` (`PRODUCT_CODE`) ON DELETE CASCADE,
  CONSTRAINT `a_price_ibfk_2` FOREIGN KEY (`PRICE_TYPE_ID`) REFERENCES `a_price_type` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=293 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_price`
--

LOCK TABLES `a_price` WRITE;
/*!40000 ALTER TABLE `a_price` DISABLE KEYS */;
INSERT INTO `a_price` VALUES (285,201,35,11.50,'2020-12-02 00:57:04'),(286,201,36,12.50,'2020-12-02 00:57:04'),(287,202,35,18.50,'2020-12-02 00:57:04'),(288,202,36,22.50,'2020-12-02 00:57:04'),(289,302,35,3010.00,'2020-12-02 00:57:04'),(290,302,36,3500.00,'2020-12-02 00:57:04'),(291,305,35,3310.00,'2020-12-02 00:57:04'),(292,305,36,2999.00,'2020-12-02 00:57:05');
/*!40000 ALTER TABLE `a_price` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_price_type`
--

DROP TABLE IF EXISTS `a_price_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_price_type` (
  `ID` int(9) NOT NULL AUTO_INCREMENT,
  `PRICE_TYPE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `PRICE_TYPE` (`PRICE_TYPE`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_price_type`
--

LOCK TABLES `a_price_type` WRITE;
/*!40000 ALTER TABLE `a_price_type` DISABLE KEYS */;
INSERT INTO `a_price_type` VALUES (36,'ÐœÐ¾ÑÐºÐ²Ð°'),(35,'Ð‘Ð°Ð·Ð¾Ð²Ð°Ñ');
/*!40000 ALTER TABLE `a_price_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_product`
--

DROP TABLE IF EXISTS `a_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_product` (
  `ID` int(9) NOT NULL AUTO_INCREMENT,
  `PRODUCT_CODE` int(9) NOT NULL,
  `PRODUCT_NAME` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `PRODUCT_CODE` (`PRODUCT_CODE`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_product`
--

LOCK TABLES `a_product` WRITE;
/*!40000 ALTER TABLE `a_product` DISABLE KEYS */;
INSERT INTO `a_product` VALUES (113,201,'Ð‘ÑƒÐ¼Ð°Ð³Ð° Ð4'),(114,202,'Ð‘ÑƒÐ¼Ð°Ð³Ð° Ð3'),(115,302,'ÐŸÑ€Ð¸Ð½Ñ‚ÐµÑ€ Canon'),(116,305,'ÐŸÑ€Ð¸Ð½Ñ‚ÐµÑ€ HP');
/*!40000 ALTER TABLE `a_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_product_category`
--

DROP TABLE IF EXISTS `a_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_product_category` (
  `PRODUCT_CODE` int(9) NOT NULL,
  `CODE_RUBRIC` int(9) NOT NULL,
  KEY `PRODUCT_CODE` (`PRODUCT_CODE`),
  KEY `CODE_RUBRIC` (`CODE_RUBRIC`),
  CONSTRAINT `a_product_category_ibfk_1` FOREIGN KEY (`PRODUCT_CODE`) REFERENCES `a_product` (`PRODUCT_CODE`) ON DELETE CASCADE,
  CONSTRAINT `a_product_category_ibfk_2` FOREIGN KEY (`CODE_RUBRIC`) REFERENCES `a_category` (`CODE_RUBRIC`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_product_category`
--

LOCK TABLES `a_product_category` WRITE;
/*!40000 ALTER TABLE `a_product_category` DISABLE KEYS */;
INSERT INTO `a_product_category` VALUES (201,7),(202,7),(302,9),(302,10),(305,9),(305,10);
/*!40000 ALTER TABLE `a_product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `a_property`
--

DROP TABLE IF EXISTS `a_property`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `a_property` (
  `ID` int(9) NOT NULL AUTO_INCREMENT,
  `PRODUCT_CODE` int(9) NOT NULL,
  `PRODUCT_PROPERTY` varchar(255) NOT NULL,
  `PROPERTY_VALUE` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `PROPERTY_VALUE` (`PROPERTY_VALUE`),
  KEY `PRODUCT_CODE` (`PRODUCT_CODE`),
  CONSTRAINT `a_property_ibfk_1` FOREIGN KEY (`PRODUCT_CODE`) REFERENCES `a_product` (`PRODUCT_CODE`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=422 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a_property`
--

LOCK TABLES `a_property` WRITE;
/*!40000 ALTER TABLE `a_property` DISABLE KEYS */;
INSERT INTO `a_property` VALUES (413,202,'Ð‘ÐµÐ»Ð¸Ð·Ð½Ð°','100'),(414,201,'Ð‘ÐµÐ»Ð¸Ð·Ð½Ð°','150'),(415,202,'ÐŸÐ»Ð¾Ñ‚Ð½Ð¾ÑÑ‚ÑŒ','90'),(417,302,'Ð¤Ð¾Ñ€Ð¼Ð°Ñ‚','A4'),(418,305,'Ð¤Ð¾Ñ€Ð¼Ð°Ñ‚','A3'),(419,302,'Ð¢Ð¸Ð¿','laser'),(421,305,'Ð¢Ð¸Ð¿','Ð›Ð°Ð·ÐµÑ€Ð½Ñ‹Ð¹');
/*!40000 ALTER TABLE `a_property` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-12-02  1:41:37
