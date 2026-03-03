-- phpMyAdmin SQL Dump
-- Base de datos: `multicinelp2`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `multicinelp2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `multicinelp2`;

-- --------------------------------------------------------
-- Tabla: usuarios
-- --------------------------------------------------------
CREATE TABLE `usuarios` (
  `CI`         varchar(20)  NOT NULL,
  `nombre`     varchar(100) NOT NULL,
  `correo`     varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `telefono`   varchar(15)  DEFAULT NULL,
  `rol`        enum('administrador','cajero','cliente') NOT NULL,
  PRIMARY KEY (`CI`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`CI`, `nombre`, `correo`, `contrasena`, `telefono`, `rol`) VALUES
('1234567',  'Admin Eddy',     'admin@multicine.com', 'admin123',  '11111111', 'administrador'),
('14785698', 'Fernando Vallez','Fernando@gmail.com',  '$2y$10$cVqJ0UAiN.a05bhq.STwH..haMKnjjoqYlN04FKyC35KjAfgZ3c8K', '12345688', 'cliente'),
('7654321',  'Cajero Pedro',   'pedro@multicine.com', 'cajero123', '22222222', 'cajero');

-- --------------------------------------------------------
-- Tabla: empleados  (hereda de usuarios via CI)
-- --------------------------------------------------------
CREATE TABLE `empleados` (
  `CI`              varchar(20) NOT NULL,
  `codigoEmpleado`  varchar(20) NOT NULL,
  `turno`           enum('mañana','tarde','noche') NOT NULL,
  `sucursal`        varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CI`),
  UNIQUE KEY `codigoEmpleado` (`codigoEmpleado`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `usuarios` (`CI`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: peliculas
-- --------------------------------------------------------
CREATE TABLE `peliculas` (
  `idPelicula`   int(11)      NOT NULL AUTO_INCREMENT,
  `titulo`       varchar(100) NOT NULL,
  `sinopsis`     text         DEFAULT NULL,
  `duracion`     int(11)      NOT NULL,
  `genero`       varchar(50)  DEFAULT NULL,
  `clasificacion`varchar(10)  DEFAULT NULL,
  `idioma`       varchar(50)  DEFAULT NULL,
  `imagenPoster` varchar(255) DEFAULT NULL,
  `estado`       enum('activa','inactiva') DEFAULT 'activa',
  PRIMARY KEY (`idPelicula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peliculas` (`idPelicula`, `titulo`, `sinopsis`, `duracion`, `genero`, `clasificacion`, `idioma`, `imagenPoster`, `estado`) VALUES
(1,  'Dragon Ball Super: Broly',   'Lucha épica entre Goku, Vegeta y Broly.',    100, 'Acción',  'B', 'Español Latino', 'BROLY.jpg',                    'activa'),
(2,  'The Super Mario Galaxy',     'Mario explora el cosmos.',                    95,  'Infantil','A', 'Español Latino', 'Mario galaxy.jpg',              'activa'),
(3,  'Avatar 2',                   'El camino del agua en Pandora.',              192, 'Sci-Fi',  'B', 'Subtitulada',    'avatar.jpg',                   'activa'),
(5,  'Titanic',                    'Un romance épico en el viaje inaugural del RMS Titanic.', 194,'Romance','B','Español Latino','titanic.jpg',          'activa'),
(6,  'Star Wars',                  'Una aventura en una galaxia muy, muy lejana.',121, 'Sci-Fi',  'B', 'Subtitulada',    'star ward.jpg',                'activa'),
(7,  'Bob Esponja: Al Rescate',    'Bob y Patricio viajan a la ciudad perdida de Atlantic City.', 91,'Infantil','A','Español Latino','bob sponja.jpg',   'activa'),
(8,  'Deadpool 3',                 'Deadpool y Wolverine en una misión caótica.', 128, 'Acción',  'C', 'Español latino', 'deadbool.jpg',                 'activa'),
(11, 'Spiderman no way Home',      'Spider-Man: No Way Home (2021) es una película de acción y ciencia ficción del Universo Cinematográfico de Marvel (MCU) donde la identidad de Peter Parker (Tom Holland) es revelada por Mysterio. Buscando ayuda en el Doctor Strange para revertir esto, un hechizo fallido rompe el multiverso, trayendo villanos de otras realidades.', 148,'Acción','B','Español latino','Spiderman No Way Home.jpg','activa');

-- --------------------------------------------------------
-- Tabla: salas  (se elimina columna redundante "capacidad", se conserva "capacidadTotal")
-- --------------------------------------------------------
CREATE TABLE `salas` (
  `idSala`        int(11)     NOT NULL AUTO_INCREMENT,
  `numero`        int(11)     NOT NULL,
  `nombre`        varchar(100) NOT NULL,
  `tipoPantalla`  enum('2D','3D','XL','4D','PLUS') NOT NULL,
  `capacidadTotal`int(11)     NOT NULL,
  `filas`         int(11)     NOT NULL DEFAULT 10,
  `columnas`      int(11)     NOT NULL DEFAULT 10,
  `precio`        decimal(10,2) NOT NULL DEFAULT 45.00,
  `tipo`          varchar(20) NOT NULL DEFAULT 'classic',
  `imagen`        varchar(255) DEFAULT 'sala_default.jpg',
  `estado`        enum('activa','mantenimiento','inactiva') DEFAULT 'activa',
  PRIMARY KEY (`idSala`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `salas` (`idSala`, `numero`, `nombre`, `tipoPantalla`, `capacidadTotal`, `filas`, `columnas`, `precio`, `tipo`, `imagen`, `estado`) VALUES
(3, 13, '',       'PLUS', 120, 10, 10, 45.00, 'classic', 'sala_default.jpg',  'activa'),
(4,  0, 'Sala 2', '2D',     0, 10, 14, 45.00, 'classic', 'sala_classic.jpg',  'activa');

-- --------------------------------------------------------
-- Tabla: asientos
-- --------------------------------------------------------
CREATE TABLE `asientos` (
  `idAsiento` int(11)  NOT NULL AUTO_INCREMENT,
  `idSala`    int(11)  NOT NULL,
  `fila`      char(1)  NOT NULL,
  `numero`    int(11)  NOT NULL,
  `estado`    enum('disponible','ocupado','mantenimiento') DEFAULT 'disponible',
  PRIMARY KEY (`idAsiento`),
  KEY `idSala` (`idSala`),
  CONSTRAINT `asientos_ibfk_1` FOREIGN KEY (`idSala`) REFERENCES `salas` (`idSala`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: funciones
-- --------------------------------------------------------
CREATE TABLE `funciones` (
  `idFuncion`       int(11)       NOT NULL AUTO_INCREMENT,
  `idPelicula`      int(11)       NOT NULL,
  `idSala`          int(11)       NOT NULL,
  `fechaFuncion`    date          NOT NULL,
  `horaInicio`      time          NOT NULL,
  `horaFin`         time          NOT NULL,
  `precioBase`      decimal(10,2) NOT NULL,
  `boletos_vendidos`int(11)       NOT NULL DEFAULT 0,
  `asientos_vendidos` text        DEFAULT NULL,
  PRIMARY KEY (`idFuncion`),
  KEY `idPelicula` (`idPelicula`),
  KEY `idSala`     (`idSala`),
  CONSTRAINT `funciones_ibfk_1` FOREIGN KEY (`idPelicula`) REFERENCES `peliculas` (`idPelicula`) ON DELETE CASCADE,
  CONSTRAINT `funciones_ibfk_2` FOREIGN KEY (`idSala`)     REFERENCES `salas`     (`idSala`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `funciones` (`idFuncion`, `idPelicula`, `idSala`, `fechaFuncion`, `horaInicio`, `horaFin`, `precioBase`, `boletos_vendidos`, `asientos_vendidos`) VALUES
(2,  3,  3, '2026-02-26', '19:00:00', '22:30:00', 75.00, 0, NULL),
(5,  8,  3, '2026-02-27', '21:00:00', '23:15:00', 75.00, 0, NULL),
(8,  5,  3, '2026-02-28', '20:00:00', '23:20:00', 80.00, 5, 'J1,J2,J3,J4,J5'),
(13, 11, 4, '2026-03-03', '12:00:00', '14:00:00', 45.00, 9, 'J1,J2,J3,J4,J5,J6,J7,I9,I10');

-- --------------------------------------------------------
-- Tabla: reservas
-- --------------------------------------------------------
CREATE TABLE `reservas` (
  `idReserva`    int(11)       NOT NULL AUTO_INCREMENT,
  `CICliente`    varchar(20)   NOT NULL,
  `idFuncion`    int(11)       NOT NULL,
  `CIEmpleado`   varchar(20)   DEFAULT NULL,
  `fechaReserva` datetime      DEFAULT current_timestamp(),
  `estado`       enum('pendiente','confirmada','cancelada') DEFAULT 'confirmada',
  `montoTotal`   decimal(10,2) NOT NULL,
  PRIMARY KEY (`idReserva`),
  KEY `CICliente`  (`CICliente`),
  KEY `idFuncion`  (`idFuncion`),
  KEY `CIEmpleado` (`CIEmpleado`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`CICliente`)  REFERENCES `usuarios`  (`CI`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`idFuncion`)  REFERENCES `funciones`  (`idFuncion`),
  CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`CIEmpleado`) REFERENCES `usuarios`  (`CI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: tickets
-- --------------------------------------------------------
CREATE TABLE `tickets` (
  `idTicket`    int(11)       NOT NULL AUTO_INCREMENT,
  `idReserva`   int(11)       NOT NULL,
  `idAsiento`   int(11)       NOT NULL,
  `codigoQR`    varchar(255)  NOT NULL,
  `precioFinal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idTicket`),
  UNIQUE KEY `codigoQR` (`codigoQR`),
  KEY `idReserva` (`idReserva`),
  KEY `idAsiento` (`idAsiento`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`idReserva`) REFERENCES `reservas` (`idReserva`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`idAsiento`) REFERENCES `asientos` (`idAsiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla: compras  (añadido: relaciona cliente con funcion y asientos)
-- --------------------------------------------------------
CREATE TABLE `compras` (
  `idCompra`      int(11)       NOT NULL AUTO_INCREMENT,
  `CI_cliente`    varchar(20)   NOT NULL,
  `idFuncion`     int(11)       NOT NULL,
  `asientos`      varchar(255)  NOT NULL,
  `total`         decimal(10,2) NOT NULL,
  `codigo_ticket` varchar(20)   NOT NULL,
  `fecha_compra`  timestamp     NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idCompra`),
  KEY `CI_cliente` (`CI_cliente`),
  KEY `idFuncion`  (`idFuncion`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`CI_cliente`) REFERENCES `usuarios`  (`CI`),
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`idFuncion`)  REFERENCES `funciones` (`idFuncion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `compras` (`idCompra`, `CI_cliente`, `idFuncion`, `asientos`, `total`, `codigo_ticket`, `fecha_compra`) VALUES
(1, '14785698', 13, 'J4, J5', 90.00, 'TK-D563F', '2026-03-01 17:02:51'),
(2, '14785698', 13, 'J6, J7', 90.00, 'TK-09A2C', '2026-03-01 17:19:57');

COMMIT;



