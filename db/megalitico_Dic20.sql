-- MySQL dump 10.11
--
-- Host: localhost    Database: megaliticodb
-- ------------------------------------------------------
-- Server version	5.0.45-community-nt-log

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
-- Table structure for table `actividades`
--

DROP TABLE IF EXISTS `actividades`;
CREATE TABLE `actividades` (
  `actividadid` int(11) NOT NULL auto_increment,
  `codigo` varchar(3) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  `realizarcada` bigint(20) NOT NULL,
  `prioritaria` varchar(1) NOT NULL,
  PRIMARY KEY  (`actividadid`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='actividades a realizar a maquinarias';

--
-- Dumping data for table `actividades`
--

LOCK TABLES `actividades` WRITE;
/*!40000 ALTER TABLE `actividades` DISABLE KEYS */;
INSERT INTO `actividades` VALUES (1,'001','REVISION GENERAL',60,'0'),(2,'002','REVISION MECANICA',30,'1'),(3,'003','CAMBIO DE ACEITE',90,'0'),(4,'004','REVISION FRENOS',60,'1'),(5,'005','REVISION DAÑOS EXTERNOS',120,'0');
/*!40000 ALTER TABLE `actividades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actividadxmaquina`
--

DROP TABLE IF EXISTS `actividadxmaquina`;
CREATE TABLE `actividadxmaquina` (
  `maquinariaid` int(11) NOT NULL,
  `actividadid` int(11) NOT NULL,
  PRIMARY KEY  (`maquinariaid`,`actividadid`),
  KEY `actividadid` (`actividadid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='actividades a realizar a maquinarias';

--
-- Dumping data for table `actividadxmaquina`
--

LOCK TABLES `actividadxmaquina` WRITE;
/*!40000 ALTER TABLE `actividadxmaquina` DISABLE KEYS */;
INSERT INTO `actividadxmaquina` VALUES (2,1),(2,3);
/*!40000 ALTER TABLE `actividadxmaquina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actrealizadas`
--

DROP TABLE IF EXISTS `actrealizadas`;
CREATE TABLE `actrealizadas` (
  `id` bigint(20) NOT NULL auto_increment,
  `mantenimid` bigint(20) NOT NULL,
  `actividadid` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mantenimid` (`mantenimid`,`actividadid`),
  KEY `actividadid` (`actividadid`),
  CONSTRAINT `actrealizadas_ibfk_1` FOREIGN KEY (`mantenimid`) REFERENCES `mantenimientos` (`mantenimid`),
  CONSTRAINT `actrealizadas_ibfk_2` FOREIGN KEY (`actividadid`) REFERENCES `actividades` (`actividadid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='Actividades Realizadas en un Mantenimiento';

--
-- Dumping data for table `actrealizadas`
--

LOCK TABLES `actrealizadas` WRITE;
/*!40000 ALTER TABLE `actrealizadas` DISABLE KEYS */;
INSERT INTO `actrealizadas` VALUES (2,8,1,'2011-10-03','05:10:19'),(3,8,2,'2011-09-10','07:09:08'),(4,8,3,'2011-09-10','07:09:08'),(5,8,4,'0000-00-00','00:00:00'),(6,8,5,'0000-00-00','00:00:00'),(7,9,1,'2011-09-12','06:09:26'),(8,9,3,'2011-09-12','06:09:26'),(9,10,1,'2011-10-03','06:10:50'),(10,10,3,'2011-10-03','06:10:50'),(11,11,1,'2011-12-20','07:12:16'),(12,11,2,'2011-12-20','07:12:16'),(13,11,3,'2011-12-20','07:12:16'),(14,11,4,'0000-00-00','00:00:00'),(15,11,5,'0000-00-00','00:00:00');
/*!40000 ALTER TABLE `actrealizadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conceptos`
--

DROP TABLE IF EXISTS `conceptos`;
CREATE TABLE `conceptos` (
  `conceptoid` int(11) NOT NULL auto_increment,
  `codigo` varchar(10) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  `creador` varchar(20) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`conceptoid`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Conceptos de Gastos del Dinero';

--
-- Dumping data for table `conceptos`
--

LOCK TABLES `conceptos` WRITE;
/*!40000 ALTER TABLE `conceptos` DISABLE KEYS */;
INSERT INTO `conceptos` VALUES (1,'001','COMPRA DE REPUESTOS','','2011-12-16 21:26:33');
/*!40000 ALTER TABLE `conceptos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE `estados` (
  `codigo` varchar(3) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  `orden` int(11) NOT NULL,
  PRIMARY KEY  (`codigo`),
  KEY `descripcion` (`descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Estados del Mantenimiento';

--
-- Dumping data for table `estados`
--

LOCK TABLES `estados` WRITE;
/*!40000 ALTER TABLE `estados` DISABLE KEYS */;
INSERT INTO `estados` VALUES ('ENR','EN REALIZACION',2),('PRO','PROGRAMADO',1),('REA','REALIZADO',3),('VEN','VENCIDO',4);
/*!40000 ALTER TABLE `estados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadosgasto`
--

DROP TABLE IF EXISTS `estadosgasto`;
CREATE TABLE `estadosgasto` (
  `codigo` varchar(3) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  `orden` int(11) NOT NULL,
  PRIMARY KEY  (`codigo`),
  KEY `descripcion` (`descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Estados de los Gastos';

--
-- Dumping data for table `estadosgasto`
--

LOCK TABLES `estadosgasto` WRITE;
/*!40000 ALTER TABLE `estadosgasto` DISABLE KEYS */;
INSERT INTO `estadosgasto` VALUES ('ENP','EN PROCESO DE PAGO',2),('PAG','PAGADO',3),('REG','GASTO REGISTRADO',1);
/*!40000 ALTER TABLE `estadosgasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadosxmante`
--

DROP TABLE IF EXISTS `estadosxmante`;
CREATE TABLE `estadosxmante` (
  `manteid` bigint(11) NOT NULL,
  `estadoid` varchar(3) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  PRIMARY KEY  (`manteid`,`estadoid`),
  KEY `estadoid` (`estadoid`),
  CONSTRAINT `estadosxmante_ibfk_2` FOREIGN KEY (`manteid`) REFERENCES `mantenimientos` (`mantenimid`),
  CONSTRAINT `estadosxmante_ibfk_3` FOREIGN KEY (`estadoid`) REFERENCES `estados` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Estados de Cada Mantenimiento';

--
-- Dumping data for table `estadosxmante`
--

LOCK TABLES `estadosxmante` WRITE;
/*!40000 ALTER TABLE `estadosxmante` DISABLE KEYS */;
INSERT INTO `estadosxmante` VALUES (8,'ENR','2011-09-10','06:09:25'),(8,'REA','2011-09-10','06:09:36'),(9,'ENR','2011-09-10','07:09:52'),(10,'REA','2011-10-03','06:10:05');
/*!40000 ALTER TABLE `estadosxmante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE `gastos` (
  `gastoid` bigint(20) NOT NULL auto_increment,
  `proveedorid` int(11) NOT NULL,
  `maquinariaid` int(11) NOT NULL,
  `conceptoid` int(11) NOT NULL,
  `valor` double NOT NULL,
  `fechahora` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `observaciones` text NOT NULL,
  `estado` varchar(3) NOT NULL,
  `creador` varchar(20) NOT NULL,
  PRIMARY KEY  (`gastoid`),
  KEY `proveedorid` (`proveedorid`,`maquinariaid`,`conceptoid`),
  KEY `maquinariaid` (`maquinariaid`),
  KEY `conceptoid` (`conceptoid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Gastos a Maquinarias';

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,2,7,1,50000,'2011-12-14 05:00:00','dfsdfsdf','PAG','ADMINISTRADOR');
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mantenimientos`
--

DROP TABLE IF EXISTS `mantenimientos`;
CREATE TABLE `mantenimientos` (
  `mantenimid` bigint(20) NOT NULL auto_increment,
  `numorden` varchar(12) NOT NULL,
  `maquinariaid` int(11) NOT NULL,
  `operariopro` bigint(11) NOT NULL,
  `operariorea` bigint(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estadoact` varchar(3) NOT NULL,
  `observacion` text NOT NULL,
  PRIMARY KEY  (`mantenimid`),
  KEY `maquinariaid` (`maquinariaid`,`operariopro`,`operariorea`),
  KEY `operariopro` (`operariopro`),
  KEY `operariorea` (`operariorea`),
  CONSTRAINT `mantenimientos_ibfk_1` FOREIGN KEY (`maquinariaid`) REFERENCES `maquinaria` (`maquinariaid`),
  CONSTRAINT `mantenimientos_ibfk_2` FOREIGN KEY (`operariopro`) REFERENCES `terceros` (`terid`),
  CONSTRAINT `mantenimientos_ibfk_3` FOREIGN KEY (`operariorea`) REFERENCES `terceros` (`terid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='mantenimientos';

--
-- Dumping data for table `mantenimientos`
--

LOCK TABLES `mantenimientos` WRITE;
/*!40000 ALTER TABLE `mantenimientos` DISABLE KEYS */;
INSERT INTO `mantenimientos` VALUES (1,'0000145',2,3,5,'2011-09-14','10:00:00','PRO','Mantenimiento Preventivo'),(2,'0000146',2,1,3,'2011-09-08','09:00:00','REA','Segundo Mantenimiento'),(3,'123',2,1,1,'2011-09-09','07:09:05','PRO',' prueba observ'),(8,'1010',3,4,1,'2011-09-09','08:09:15','ENR',' Todas las Actividades'),(9,'789',2,3,5,'2011-09-10','07:09:58','ENR',' '),(10,'',2,6,6,'2011-09-29','09:00:00','REA','Se realiza cambios de:\r\nAceite Motor, Aceite Hidraúlico\r\nFiltro Combustible  primario y Secundario\r\nFiltro Aire  primario y Secundario  '),(11,'89652',3,7,7,'2011-12-20','07:12:45','PRO',' cambiar llantas');
/*!40000 ALTER TABLE `mantenimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maquinaria`
--

DROP TABLE IF EXISTS `maquinaria`;
CREATE TABLE `maquinaria` (
  `maquinariaid` int(11) NOT NULL auto_increment,
  `codigo` varchar(12) NOT NULL,
  `descripcion` varchar(40) NOT NULL,
  `marca` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `modelo` varchar(15) NOT NULL,
  `serie` varchar(25) NOT NULL,
  `ano` varchar(4) NOT NULL,
  `color` varchar(15) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `feccompra` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `foto` varchar(50) NOT NULL,
  PRIMARY KEY  (`maquinariaid`),
  KEY `codigo` (`codigo`),
  KEY `tipo` (`tipo`),
  KEY `marca` (`marca`),
  CONSTRAINT `maquinaria_ibfk_1` FOREIGN KEY (`marca`) REFERENCES `marcamaq` (`marcaid`),
  CONSTRAINT `maquinaria_ibfk_2` FOREIGN KEY (`tipo`) REFERENCES `tipomaq` (`tipoid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Maquinaria de la Empresa';

--
-- Dumping data for table `maquinaria`
--

LOCK TABLES `maquinaria` WRITE;
/*!40000 ALTER TABLE `maquinaria` DISABLE KEYS */;
INSERT INTO `maquinaria` VALUES (2,'04','RETROEXCAVADORA 4',1,1,'420','CAT0420DVBLN08400','2004','','NO TIENE','2011-04-14 05:00:00',''),(3,'01','RETROEXCAVADORA 1',2,1,'310G123456789','T0310GX934373','2004','',' NO TIENE','2011-04-01 05:00:00',''),(4,'02','RETROEXCAVADORA 2',1,1,'310G','T031GX944786','2005','','NO TIENE','2010-04-04 05:00:00',''),(5,'03','RETROEXCAVADORA 3',1,1,'310G','T0310GX946523','2005','','NO TIENE','2010-12-13 05:00:00',''),(6,'05','EXCAVADORA SOBRE ORUGA PC120-6E',3,1,'PC12','64391','2000','','NO TIENE','2011-03-25 05:00:00',''),(7,'06','MINICARGADOR',4,1,'S175','525211091','2006','','NO TIENE','2011-04-14 05:00:00','');
/*!40000 ALTER TABLE `maquinaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcamaq`
--

DROP TABLE IF EXISTS `marcamaq`;
CREATE TABLE `marcamaq` (
  `marcaid` int(11) NOT NULL auto_increment,
  `codigo` varchar(6) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  PRIMARY KEY  (`marcaid`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Marca de Maquinaria';

--
-- Dumping data for table `marcamaq`
--

LOCK TABLES `marcamaq` WRITE;
/*!40000 ALTER TABLE `marcamaq` DISABLE KEYS */;
INSERT INTO `marcamaq` VALUES (1,'J D','JOHN DEERE'),(2,'CAT','CATERPILLAR'),(3,'KTSU','KOMATSU'),(4,'BOB','BOBCAT');
/*!40000 ALTER TABLE `marcamaq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `proveedorid` int(11) NOT NULL auto_increment,
  `nit` varchar(15) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `regimen` varchar(12) NOT NULL,
  `creador` varchar(15) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`proveedorid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (2,'6664195','SISTEMAS Y SOLUCIONES WEB DE COLOMBIA','CALLE 2 NO. 7-33 BARRIO BELLAVISTA','3002692042','saidrodriguez@gmail.com','SIMPLIFICADO','ADMINISTRADOR','2011-12-06 20:04:26');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terceros`
--

DROP TABLE IF EXISTS `terceros`;
CREATE TABLE `terceros` (
  `terid` bigint(20) NOT NULL auto_increment,
  `codigo` varchar(15) NOT NULL,
  `nit` varchar(15) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `direccion` varchar(60) NOT NULL,
  `ciudadid` bigint(11) NOT NULL,
  `telefono` varchar(25) NOT NULL,
  `celular` varchar(25) NOT NULL,
  `email` varchar(60) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `sueldo` varchar(30) NOT NULL,
  `bonificacion` varchar(30) NOT NULL,
  `eps` varchar(30) NOT NULL,
  `afp` varchar(30) NOT NULL,
  `ccf` varchar(30) NOT NULL,
  `arp` varchar(30) NOT NULL,
  `cuenta` varchar(30) NOT NULL,
  `tipocuenta` varchar(15) NOT NULL,
  `fecingreso` varchar(12) NOT NULL,
  `fecretiro` varchar(12) NOT NULL,
  `feccontrato` varchar(12) NOT NULL,
  `creador` varchar(20) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`terid`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nit` (`nit`),
  KEY `ciudadid` (`ciudadid`),
  KEY `creador` (`creador`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Terceros';

--
-- Dumping data for table `terceros`
--

LOCK TABLES `terceros` WRITE;
/*!40000 ALTER TABLE `terceros` DISABLE KEYS */;
INSERT INTO `terceros` VALUES (1,'004','4150302','OVIDIO SALAZAR BOHORQUEZ','CR 5 NO 18-32 SENDEROS DE PAZ, VILLA DEL ROSARIO',52,'NO TIENE','3123536642','ovidiosalazar1964@hotmail.com','','','','','','','','','','','','','ADMINISTRADOR','2011-10-07 16:25:27'),(3,'007','13718109','ELKIN ACEVEDO VALENCIA','CALLE 59 NO 42W20 ARSELLA, BUCARAMANGA',740,'NO TIENE','3117619524','no tiene','','','','','','','','','','','','','ADMINISTRADOR','2011-10-03 17:18:22'),(4,'005','5415898','JUAN JOSE BOHORQUEZ ','NO TIENE',694,'NO TIENE','3123193722','no tiene','','','','','','','','','','','','','ADMINISTRADOR','2011-10-03 17:16:17'),(5,'006','10174404','GENCY RAFAEL OSORIO','BOSQUE DEL CACIQUE MANZANA 25 CASA #3 – FLORIDABLANCA, B/MAN',0,'NO TIENE','3166362897','no tiene','','','','','','','','','','','','','','2011-10-03 17:17:17'),(6,'008','85201489','JESÚS ALBERTO GAZCÓN PEREZ','CRA 8A NO 27-26 URBANIZACIÓN MUNDO LOPEZ, SANTA ANA, MAGDALE',0,'NO TIENE','3114257409','no tiene','','','','','','','','','','','','','','2011-10-03 17:19:26'),(7,'009','13872967','EDUAR MENDOZA MENDOZA','VEREDA PESCADITO - CEPITÁ (VÍA A SAN GIL)',0,'NO TIENE','3133574620','tiger_cepita@hotmail.com','','','','','','','','','AHORROS','10/10/2011','11/10/2011','12/10/2011','','2011-12-20 12:12:18');
/*!40000 ALTER TABLE `terceros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipomaq`
--

DROP TABLE IF EXISTS `tipomaq`;
CREATE TABLE `tipomaq` (
  `tipoid` int(11) NOT NULL auto_increment,
  `codigo` varchar(6) NOT NULL,
  `descripcion` varchar(30) NOT NULL,
  PRIMARY KEY  (`tipoid`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Tipos de Maquinaria';

--
-- Dumping data for table `tipomaq`
--

LOCK TABLES `tipomaq` WRITE;
/*!40000 ALTER TABLE `tipomaq` DISABLE KEYS */;
INSERT INTO `tipomaq` VALUES (1,'PES','PESADA'),(2,'MENOR','EQUIPOS MENORES');
/*!40000 ALTER TABLE `tipomaq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `username` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `clave` varchar(32) NOT NULL,
  `email` varchar(60) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES ('administrador','Administrador del Sistema','jimena','admin@localhost','0000-00-00 00:00:00'),('said','SAID RODRIGUEZ','d2218aa','','2011-07-21 19:37:57');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'megaliticodb'
--
DELIMITER ;;
DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-12-20 13:08:32
