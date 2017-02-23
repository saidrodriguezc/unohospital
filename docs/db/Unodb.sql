-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Servidor: localhost
-- Tiempo de generación: 24-05-2011 a las 07:22:05
-- Versión del servidor: 5.0.45
-- Versión de PHP: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Base de datos: `centineladb`
-- 

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `bodegas`
-- 

CREATE TABLE `bodegas` (
  `bodegaid` int(11) NOT NULL auto_increment,
  `codigo` varchar(5) NOT NULL,
  `descripcion` varchar(20) NOT NULL,
  `creador` varchar(20) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`bodegaid`),
  KEY `codigo` (`codigo`),
  KEY `creador` (`creador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Bodegas de Inventarios' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `bodegas`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `ciudades`
-- 

CREATE TABLE `ciudades` (
  `ciudadid` bigint(20) NOT NULL auto_increment,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `departamento` varchar(25) NOT NULL,
  `creador` varchar(20) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ciudadid`),
  KEY `codigo` (`codigo`),
  KEY `creador` (`creador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ciudades - Localidades' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `ciudades`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `clasificater`
-- 

CREATE TABLE `clasificater` (
  `clasificaterid` int(11) NOT NULL auto_increment,
  `codigo` varchar(5) NOT NULL,
  `descripcion` varchar(25) NOT NULL,
  PRIMARY KEY  (`clasificaterid`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Clasificacion de Terceros' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `clasificater`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `item`
-- 

CREATE TABLE `item` (
  `itemid` bigint(20) NOT NULL auto_increment,
  `codigo` varchar(10) NOT NULL,
  `descripcion` varchar(35) NOT NULL,
  `tipoitemid` varchar(3) NOT NULL,
  PRIMARY KEY  (`itemid`),
  KEY `codigo` (`codigo`,`tipoitemid`),
  KEY `tipoitemid` (`tipoitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Items del Sistema' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `item`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `menu`
-- 

CREATE TABLE `menu` (
  `menuid` int(11) NOT NULL auto_increment,
  `orden` varchar(8) NOT NULL,
  `descripcion` varchar(25) NOT NULL,
  `link` varchar(50) NOT NULL,
  PRIMARY KEY  (`menuid`),
  KEY `orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menus del Sistema' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `menu`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `menuxusuario`
-- 

CREATE TABLE `menuxusuario` (
  `menuid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `permitido` varchar(1) NOT NULL,
  `creador` varchar(20) NOT NULL,
  PRIMARY KEY  (`menuid`,`username`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Menu de Usuarios del Sistema';

-- 
-- Volcar la base de datos para la tabla `menuxusuario`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `permisos`
-- 

CREATE TABLE `permisos` (
  `permisoid` int(11) NOT NULL auto_increment,
  `descripcion` varchar(25) NOT NULL,
  PRIMARY KEY  (`permisoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permisos del Sistema' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `permisos`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `permisosxusuario`
-- 

CREATE TABLE `permisosxusuario` (
  `permisoid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `permitido` varchar(1) NOT NULL,
  `creador` varchar(20) NOT NULL,
  PRIMARY KEY  (`permisoid`,`username`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permisos a Usuarios del Sistema';

-- 
-- Volcar la base de datos para la tabla `permisosxusuario`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `prefijo`
-- 

CREATE TABLE `prefijo` (
  `prefijo` varchar(3) NOT NULL,
  `tipodoc` varchar(3) NOT NULL,
  `descripcion` varchar(20) NOT NULL,
  PRIMARY KEY  (`prefijo`),
  KEY `tipodoc` (`tipodoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Prefijos de Documentos';

-- 
-- Volcar la base de datos para la tabla `prefijo`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `productos`
-- 

CREATE TABLE `productos` (
  `itemid` bigint(20) NOT NULL,
  `unidad` varchar(5) NOT NULL,
  `unimayor` varchar(5) NOT NULL,
  `eximin` int(11) NOT NULL,
  `eximax` int(11) NOT NULL,
  PRIMARY KEY  (`itemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Productos de Inventarios';

-- 
-- Volcar la base de datos para la tabla `productos`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `sucursales`
-- 

CREATE TABLE `sucursales` (
  `sucid` int(11) NOT NULL auto_increment,
  `codigo` varchar(5) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `creador` varchar(20) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sucid`),
  KEY `codigo` (`codigo`),
  KEY `creador` (`creador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Sucursales de la Empresa' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `sucursales`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `terceros`
-- 

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
  `zonaid` int(11) NOT NULL,
  `clasificaterid` int(11) NOT NULL,
  `clientedesde` date default NULL,
  `provdesde` date default NULL,
  `creador` varchar(20) NOT NULL,
  `momento` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`terid`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `nit` (`nit`),
  KEY `ciudadid` (`ciudadid`),
  KEY `zonaid` (`zonaid`,`clasificaterid`),
  KEY `clasificaterid` (`clasificaterid`),
  KEY `creador` (`creador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Terceros' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `terceros`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tipodoc`
-- 

CREATE TABLE `tipodoc` (
  `codigo` varchar(3) NOT NULL,
  `descripcion` varchar(20) NOT NULL,
  PRIMARY KEY  (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tipo de Documento';

-- 
-- Volcar la base de datos para la tabla `tipodoc`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `tipoitem`
-- 

CREATE TABLE `tipoitem` (
  `tipoitemid` varchar(3) NOT NULL,
  `descripcion` varchar(25) NOT NULL,
  PRIMARY KEY  (`tipoitemid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tipos de Item';

-- 
-- Volcar la base de datos para la tabla `tipoitem`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `usuarios`
-- 

CREATE TABLE `usuarios` (
  `username` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `clave` varchar(32) NOT NULL,
  `email` varchar(60) NOT NULL,
  PRIMARY KEY  (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Usuarios del Sistema';

-- 
-- Volcar la base de datos para la tabla `usuarios`
-- 


-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `zonater`
-- 

CREATE TABLE `zonater` (
  `zonaid` int(11) NOT NULL auto_increment,
  `codigo` varchar(5) NOT NULL,
  `descripcion` varchar(25) NOT NULL,
  PRIMARY KEY  (`zonaid`),
  KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Zonas de Terceros' AUTO_INCREMENT=1 ;

-- 
-- Volcar la base de datos para la tabla `zonater`
-- 


-- 
-- Filtros para las tablas descargadas (dump)
-- 

-- 
-- Filtros para la tabla `item`
-- 
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`tipoitemid`) REFERENCES `tipoitem` (`tipoitemid`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `menuxusuario`
-- 
ALTER TABLE `menuxusuario`
  ADD CONSTRAINT `menuxusuario_ibfk_1` FOREIGN KEY (`menuid`) REFERENCES `menu` (`menuid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menuxusuario_ibfk_2` FOREIGN KEY (`username`) REFERENCES `usuarios` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `permisosxusuario`
-- 
ALTER TABLE `permisosxusuario`
  ADD CONSTRAINT `permisosxusuario_ibfk_1` FOREIGN KEY (`permisoid`) REFERENCES `permisos` (`permisoid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permisosxusuario_ibfk_2` FOREIGN KEY (`username`) REFERENCES `usuarios` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `prefijo`
-- 
ALTER TABLE `prefijo`
  ADD CONSTRAINT `prefijo_ibfk_1` FOREIGN KEY (`tipodoc`) REFERENCES `tipodoc` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Filtros para la tabla `terceros`
-- 
ALTER TABLE `terceros`
  ADD CONSTRAINT `terceros_ibfk_1` FOREIGN KEY (`ciudadid`) REFERENCES `ciudades` (`ciudadid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `terceros_ibfk_2` FOREIGN KEY (`zonaid`) REFERENCES `zonater` (`zonaid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `terceros_ibfk_3` FOREIGN KEY (`clasificaterid`) REFERENCES `clasificater` (`clasificaterid`) ON DELETE CASCADE ON UPDATE CASCADE;
