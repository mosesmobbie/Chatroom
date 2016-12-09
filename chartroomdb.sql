/*
SQLyog Community v12.2.4 (32 bit)
MySQL - 10.1.19-MariaDB : Database - chatroomdb
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`chatroomdb` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `chatroomdb`;

/*Table structure for table `chats` */

DROP TABLE IF EXISTS `chats`;

CREATE TABLE `chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `message` text NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

/*Data for the table `chats` */

insert  into `chats`(`id`,`datetime`,`message`,`from_user_id`,`to_user_id`,`read_status`) values 
(1,'2016-12-07 17:22:15','Hello MsKay',3,2,1),
(2,'2016-12-07 17:22:37','Hi Shadrack',2,3,1),
(3,'2016-12-07 17:23:03','Kunjhani ka Shady',2,3,1),
(4,'2016-12-07 17:24:26','Ni kona, Minjhani Mrs Mobbie',3,2,1),
(29,'2016-12-08 11:05:45','nikona',2,3,1),
(30,'2016-12-08 11:05:59','swi sasekile',3,2,1),
(31,'2016-12-08 11:20:21','Hi Moses',2,1,1),
(32,'2016-12-08 11:20:28','Hello',1,2,1),
(33,'2016-12-08 11:20:38','kunjhani',2,1,1),
(34,'2016-12-08 11:20:44','ni kona kunjhani',1,2,1),
(35,'2016-12-08 11:21:29','Shady we',1,3,1),
(36,'2016-12-08 11:36:01','Hi Moss',3,1,1),
(37,'2016-12-08 11:36:14','shadrack',1,3,1),
(38,'2016-12-08 11:36:36','this is beautiful',3,2,1),
(39,'2016-12-08 11:36:53','hello',1,2,1),
(40,'2016-12-09 08:07:30','test',3,2,1),
(41,'2016-12-09 08:07:44','okay\n',2,3,1),
(42,'2016-12-09 08:08:03','where is our old data',3,2,1),
(43,'2016-12-09 08:08:13','dont know\n',2,3,1),
(44,'2016-12-09 08:35:28','okay',2,3,1),
(45,'2016-12-09 08:39:24','yah it works',3,2,1),
(46,'2016-12-09 08:39:46','if you say so',2,3,1),
(47,'2016-12-09 08:40:03','positive',3,2,1),
(48,'2016-12-09 08:53:12','continue',2,3,1);

/*Table structure for table `session` */

DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `session` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `firstname` varchar(45) NOT NULL,
  `surname` varchar(45) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`id`,`datetime`,`firstname`,`surname`,`username`,`password`,`phone`,`gender`) values 
(1,'2016-12-09 08:45:55','Moss','Mobbie','moss@test.co.za','0ea3be604e1bda33e7572760a8c896231f034efb','0781887508',0),
(2,'2016-12-09 08:47:24','MsKay','Mobbie','mskay@test.co.za','f55f559ef9d46ce9be528e7737d7e59d6828a862','0727455016',1),
(3,'2016-12-09 08:48:49','Shady','Jack','shady@test.co.za','4ba2019149ccdf1737feb1c5c7e7c40c75c7ade1','0123456789',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
