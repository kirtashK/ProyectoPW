-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-03-2024 a las 13:25:34
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pw_vuelo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `idReserva` int(11) NOT NULL,
  `idVuelo` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fechaReserva` date NOT NULL,
  `Precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`idReserva`, `idVuelo`, `idUsuario`, `fechaReserva`, `Precio`) VALUES
(1, 54, 32088762, '2024-03-27', 50.23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `dni` int(11) NOT NULL,
  `contrasena` varchar(30) NOT NULL,
  `rol` enum('admin','usuario','','') NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `apellidos` varchar(60) NOT NULL,
  `correo` varchar(30) NOT NULL,
  `fechaNacimiento` date NOT NULL,
  `Saldo` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`dni`, `contrasena`, `rol`, `nombre`, `apellidos`, `correo`, `fechaNacimiento`, `Saldo`) VALUES
(32088762, 'pepe', 'usuario', 'Marcos', 'Morales', 'marcos@hotmail.com', '2001-06-24', 5000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelos`
--

CREATE TABLE `vuelos` (
  `id` int(11) NOT NULL,
  `origen` varchar(30) NOT NULL,
  `destino` varchar(30) NOT NULL,
  `fecha` date NOT NULL,
  `hora_salida` time NOT NULL,
  `hora_llegada` time NOT NULL,
  `aerolinea` varchar(30) NOT NULL,
  `capacidad` int(11) NOT NULL DEFAULT 80,
  `precio` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vuelos`
--

INSERT INTO `vuelos` (`id`, `origen`, `destino`, `fecha`, `hora_salida`, `hora_llegada`, `aerolinea`, `capacidad`, `precio`) VALUES
(0, 'Madrid', 'Barcelona', '2024-03-05', '08:00:00', '10:00:00', 'Iberia', 80, 150),
(1, 'Barcelona', 'Sevilla', '2024-03-06', '09:30:00', '11:30:00', 'Vueling', 80, 200),
(2, 'Sevilla', 'Valencia', '2024-03-07', '11:00:00', '13:00:00', 'Air Europa', 80, 180),
(3, 'Valencia', 'Málaga', '2024-03-08', '10:00:00', '12:00:00', 'Iberia Express', 80, 220),
(4, 'Málaga', 'Bilbao', '2024-03-09', '12:30:00', '14:30:00', 'Air Nostrum', 80, 190),
(5, 'Bilbao', 'Alicante', '2024-03-10', '08:00:00', '10:00:00', 'Volotea', 80, 160),
(6, 'Alicante', 'Granada', '2024-03-11', '09:30:00', '11:30:00', 'Iberia', 80, 210),
(7, 'Granada', 'Palma de Mallorca', '2024-03-12', '11:00:00', '13:00:00', 'Air Europa', 80, 230),
(8, 'Palma de Mallorca', 'Santiago de Compostela', '2024-03-13', '10:00:00', '12:00:00', 'Vueling', 80, 240),
(9, 'Santiago de Compostela', 'Tenerife', '2024-03-14', '12:30:00', '14:30:00', 'Iberia Express', 80, 270),
(10, 'Tenerife', 'Lanzarote', '2024-03-15', '08:00:00', '10:00:00', 'Air Nostrum', 80, 250),
(11, 'Lanzarote', 'Ibiza', '2024-03-16', '09:30:00', '11:30:00', 'Volotea', 80, 220),
(12, 'Ibiza', 'Fuerteventura', '2024-03-17', '11:00:00', '13:00:00', 'Iberia', 80, 280),
(13, 'Fuerteventura', 'Menorca', '2024-03-18', '10:00:00', '12:00:00', 'Air Europa', 80, 260),
(14, 'Menorca', 'La Palma', '2024-03-19', '12:30:00', '14:30:00', 'Vueling', 80, 270),
(15, 'La Palma', 'Girona', '2024-03-20', '08:00:00', '10:00:00', 'Iberia Express', 80, 230),
(16, 'Girona', 'Pamplona', '2024-03-21', '09:30:00', '11:30:00', 'Air Nostrum', 80, 210),
(17, 'Pamplona', 'Zaragoza', '2024-03-22', '11:00:00', '13:00:00', 'Volotea', 80, 200),
(18, 'Zaragoza', 'Toledo', '2024-03-23', '10:00:00', '12:00:00', 'Iberia', 80, 190),
(19, 'Toledo', 'Cáceres', '2024-03-24', '12:30:00', '14:30:00', 'Air Europa', 80, 220),
(20, 'Barcelona', 'Madrid', '2024-03-25', '08:00:00', '10:00:00', 'Vueling', 80, 160),
(21, 'Sevilla', 'Barcelona', '2024-03-26', '09:30:00', '11:30:00', 'Iberia', 80, 180),
(22, 'Valencia', 'Sevilla', '2024-03-27', '11:00:00', '13:00:00', 'Air Europa', 80, 200),
(23, 'Málaga', 'Valencia', '2024-03-28', '10:00:00', '12:00:00', 'Iberia Express', 80, 220),
(24, 'Bilbao', 'Málaga', '2024-03-29', '12:30:00', '14:30:00', 'Air Nostrum', 80, 240),
(25, 'Alicante', 'Bilbao', '2024-03-30', '08:00:00', '10:00:00', 'Volotea', 80, 200),
(26, 'Granada', 'Alicante', '2024-03-31', '09:30:00', '11:30:00', 'Iberia', 80, 210),
(27, 'Palma de Mallorca', 'Granada', '2024-04-01', '11:00:00', '13:00:00', 'Air Europa', 80, 230),
(28, 'Santiago de Compostela', 'Palma de Mallorca', '2024-04-02', '10:00:00', '12:00:00', 'Vueling', 80, 240),
(29, 'Tenerife', 'Santiago de Compostela', '2024-04-03', '12:30:00', '14:30:00', 'Iberia Express', 80, 270),
(30, 'Lanzarote', 'Tenerife', '2024-04-04', '08:00:00', '10:00:00', 'Air Nostrum', 80, 250),
(31, 'Ibiza', 'Lanzarote', '2024-04-05', '09:30:00', '11:30:00', 'Volotea', 80, 220),
(32, 'Fuerteventura', 'Ibiza', '2024-04-06', '11:00:00', '13:00:00', 'Iberia', 80, 280),
(33, 'Menorca', 'Fuerteventura', '2024-04-07', '10:00:00', '12:00:00', 'Air Europa', 80, 260),
(34, 'La Palma', 'Menorca', '2024-04-08', '12:30:00', '14:30:00', 'Vueling', 80, 270),
(35, 'Girona', 'La Palma', '2024-04-09', '08:00:00', '10:00:00', 'Iberia Express', 80, 230),
(36, 'Pamplona', 'Girona', '2024-04-10', '09:30:00', '11:30:00', 'Air Nostrum', 80, 210),
(37, 'Zaragoza', 'Pamplona', '2024-04-11', '11:00:00', '13:00:00', 'Volotea', 80, 200),
(38, 'Toledo', 'Zaragoza', '2024-04-12', '10:00:00', '12:00:00', 'Iberia', 80, 190),
(39, 'Cáceres', 'Toledo', '2024-04-13', '12:30:00', '14:30:00', 'Air Europa', 80, 220),
(40, 'Madrid', 'Barcelona', '2024-04-14', '08:00:00', '10:00:00', 'Iberia', 80, 150),
(41, 'Barcelona', 'Sevilla', '2024-04-15', '09:30:00', '11:30:00', 'Vueling', 80, 200),
(42, 'Sevilla', 'Valencia', '2024-04-16', '11:00:00', '13:00:00', 'Air Europa', 80, 180),
(43, 'Valencia', 'Málaga', '2024-04-17', '10:00:00', '12:00:00', 'Iberia Express', 80, 220),
(44, 'Málaga', 'Bilbao', '2024-04-18', '12:30:00', '14:30:00', 'Air Nostrum', 80, 190),
(45, 'Bilbao', 'Alicante', '2024-04-19', '08:00:00', '10:00:00', 'Volotea', 80, 160),
(46, 'Alicante', 'Granada', '2024-04-20', '09:30:00', '11:30:00', 'Iberia', 80, 210),
(47, 'Granada', 'Palma de Mallorca', '2024-04-21', '11:00:00', '13:00:00', 'Air Europa', 80, 230),
(48, 'Palma de Mallorca', 'Santiago de Compostela', '2024-04-22', '10:00:00', '12:00:00', 'Vueling', 80, 240),
(49, 'Santiago de Compostela', 'Tenerife', '2024-04-23', '12:30:00', '14:30:00', 'Iberia Express', 80, 270),
(54, 'Jerez', 'Madrid', '2024-03-05', '13:05:00', '00:00:00', 'Iberia', 80, 50.23);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`idReserva`),
  ADD KEY `FK_idUsuario` (`idUsuario`),
  ADD KEY `FK_idVuelo` (`idVuelo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`dni`);

--
-- Indices de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `idReserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `FK_idUsuario` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_idVuelo` FOREIGN KEY (`idVuelo`) REFERENCES `vuelos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
