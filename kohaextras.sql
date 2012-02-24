/*
SQLyog Community Edition- MySQL GUI v6.16
MySQL - 5.0.51a : Database - kohaextras
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `kohaextras`;

USE `kohaextras`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `holiday` */

DROP TABLE IF EXISTS `holiday`;

CREATE TABLE `holiday` (
  `holdate` date default NULL,
  `holname` varchar(30) collate latin1_general_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `holiday` */

insert  into `holiday`(`holdate`,`holname`) values ('2007-04-08','Easter Sunday'),('2007-05-27','Sunday before Memorial Day'),('2007-05-28','Memorial Day'),('2007-07-04','July 4th'),('2007-09-02','Day before Labor Day'),('2007-09-03','Labor Day'),('2007-11-22','Thanksgiving'),('2007-12-24','Christmas Eve'),('2007-12-25','Christmas'),('2008-01-01','New Years Day'),('2007-11-14','In-Service Day');

/*Table structure for table `inhouseuse` */

DROP TABLE IF EXISTS `inhouseuse`;

CREATE TABLE `inhouseuse` (
  `houseuse` int(11) default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP,
  `staffname` varchar(20) collate latin1_general_ci default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `inhouseuse` */

/*Table structure for table `listill` */

DROP TABLE IF EXISTS `listill`;

CREATE TABLE `listill` (
  `title` varchar(30) collate latin1_general_ci default NULL,
  `collection` varchar(30) collate latin1_general_ci default NULL,
  `callno` varchar(30) collate latin1_general_ci default NULL,
  `itype` varchar(30) collate latin1_general_ci default NULL,
  `barcode` varchar(30) collate latin1_general_ci default NULL,
  `patronid` varchar(30) collate latin1_general_ci default NULL,
  `time` time default NULL,
  `date` date default NULL,
  `name` varchar(50) collate latin1_general_ci default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `listill` */

/*Table structure for table `spinelabel` */

DROP TABLE IF EXISTS `spinelabel`;

CREATE TABLE `spinelabel` (
  `location` varchar(30) collate latin1_general_ci NOT NULL,
  `spine` varchar(30) collate latin1_general_ci default NULL,
  PRIMARY KEY  (`location`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `spinelabel` */

insert  into `spinelabel`(`location`,`spine`) values ('ADED','ANR'),('ADEDF','ANR'),('AGN',''),('ATLAS',''),('AUTO','AUTO'),('BBOCD','B'),('BBOT','B'),('BDVD','B'),('BIO','B'),('BOCD','BOOK/CD'),('BOCDNF','BOOK/CD'),('BVID','B'),('CA',''),('CAREER',''),('CD',''),('CDROM','CDROM'),('CDROMDOC','CDROM'),('CDROMNF','CDROM'),('CDROMREF','CDROM'),('CLPB',''),('CR','ANR'),('CS',''),('CX',''),('DVD','DVD'),('EXPR',''),('EXPRBIO','B'),('EXPRLP','LP'),('EXPRMYS',''),('EXPRNF',''),('EXPRSF',''),('EXPRWEST',''),('FANT',''),('FANTPB',''),('FAX',''),('FIC',''),('GIFT',''),('ILLASCPL',''),('ILLCAMLS',''),('ILLOCLC',''),('ILLOTHER',''),('INSP',''),('JA','J/AUDIO'),('JBA','JB/AUDIO'),('JBD','BD'),('JBIO','JB'),('JBOCD','JBOOK/CD'),('JCAL',''),('JCALNF',''),('JCD','J CD'),('JCDROM','JCDROM'),('JCDROMDOC','JCDROM'),('JCDROMNF','JCDROM'),('JCP','JP'),('JDVD','JDVD'),('JE','JE'),('JEA','JE/AUDIO'),('JEPB','JE'),('JF','J'),('JFL','FLcards'),('JFN','J'),('JFS','J'),('JFV','J'),('JFVN','J'),('JGN','J'),('JM','J'),('JNDVD','DVD'),('JNEWBIO','JB'),('JNEWFIC','J'),('JNEWJE','JE'),('JNEWJP','JP'),('JNEWNF','J'),('JNEWPSFIC','RD'),('JNF','J'),('JNFA','J/AUDIO'),('JNFDVD','JDVD'),('JNFN','J'),('JNFS','J'),('JNFV','VIDEO'),('JP','JP'),('JPA','JP/AUDIO'),('JPB','J'),('JPBS','J'),('JPT','J'),('JPTF','J'),('JPUP',''),('JPZ',''),('JR','J REF'),('JRD','RD'),('JREC',''),('JRR','JP'),('JRRNF',''),('JSH','JSH'),('JSK',''),('JSOFT',''),('JTD','TD'),('LHIST','LHIST'),('LPBIO','LPBIO'),('LPFIC','LP'),('LPNF','LP'),('LPPER',''),('M/S',''),('MICRO',''),('MYS',''),('MYSPB',''),('NDVD','DVD'),('NEWBIO','B'),('NEWCD',''),('NEWFIC',''),('NEWINSP',''),('NEWLP','LP'),('NEWMYS',''),('NEWNF',''),('NEWNFDVD','DVD'),('NEWS',''),('NEWSC',''),('NEWSF',''),('NEWSL',''),('NEWWEST',''),('NEWYAFIC',''),('NEWYANF',''),('NF',''),('NFDVD','DVD'),('NVF',''),('OUTR','LP'),('OV','OVERSIZE'),('PB',''),('PER',''),('PLAYAWY',''),('PRO','PROF'),('PROPER',''),('R&W FEST',''),('REF','REF'),('REFAUTO','REF'),('REFBUS','BUS REF'),('REFCON','CON REF'),('REFCONPER','REF'),('REFDESK','DESKREF'),('REFINDEX','REF'),('REFLAW','LAW REF'),('REFSTACKS','REF'),('REFTAX','TAX REF'),('ROMAPB',''),('SC',''),('SD',''),('SF',''),('SFPB',''),('SOFT',''),('TESTPREP','TEST'),('TPLAY','TEEN/AV'),('TRAVEL',''),('VB',''),('VF',''),('VN','VIDEO'),('VR',''),('WEST',''),('WESTPB',''),('YAAV','TEEN/AV'),('YAFIC',''),('YANF',''),('YAPB',''),('YAPBS',''),('YAPER',''),('YAREF','TEENREF'),('YARES',''),('YGN',''),('NEWFANT',''),('EXPRDVD',''),('JBIG','JBIG'),('ORD','');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
