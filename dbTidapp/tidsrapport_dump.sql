/*
SQLyog Community
MySQL - 5.7.36 : Database - tidsrapport
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `kategorier` */

CREATE TABLE `kategorier` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Kategori` varchar(30) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UIX_Kategori` (`Kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

/*Data for the table `kategorier` */

insert  into `kategorier`(`ID`,`Kategori`) values 
(1,'css'),
(2,'HTML'),
(3,'Javascript'),
(4,'PHP'),
(5,'Sitter');

/*Table structure for table `uppgifter` */

CREATE TABLE `uppgifter` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Tid` time NOT NULL,
  `Datum` date NOT NULL,
  `KategoriID` int(11) NOT NULL,
  `Beskrivning` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `KategoriID` (`KategoriID`),
  CONSTRAINT `uppgifter_ibfk_1` FOREIGN KEY (`KategoriID`) REFERENCES `kategorier` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

/*Data for the table `uppgifter` */

insert  into `uppgifter`(`ID`,`Tid`,`Datum`,`KategoriID`,`Beskrivning`) values 
(1,'01:15:00','2023-01-10',2,'Lagad'),
(2,'01:00:00','2023-01-10',3,'Nytt api för väder'),
(3,'01:00:00','2023-01-10',1,'Lagade nya buttons'),
(4,'05:00:00','2023-01-10',5,'Satt mycket, ont i ryggraden');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
