-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-03-2025 a las 21:47:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(255) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Bellota'),
(2, 'Serrano'),
(3, 'Ibérico'),
(4, 'Jabugo'),
(5, 'York'),
(6, 'Pavo'),
(7, 'Trevélez'),
(8, 'Pata Negra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas_pedidos`
--

CREATE TABLE `lineas_pedidos` (
  `id` int(255) NOT NULL,
  `pedido_id` int(255) NOT NULL,
  `producto_id` int(255) NOT NULL,
  `unidades` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(255) NOT NULL,
  `usuario_id` int(255) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `coste` float(200,2) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(255) NOT NULL,
  `categoria_id` int(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` float(100,2) NOT NULL,
  `stock` int(255) NOT NULL,
  `oferta` varchar(2) NOT NULL,
  `fecha` date NOT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `oferta`, `fecha`, `imagen`) VALUES
(1, 5, 'Jamón York de Murgati', 'Jamón York envasado de la empresa Mugarti, muy rico por cierto', 2.11, 6, 'Si', '2025-03-02', 'jamonyorkMurgati.png'),
(2, 8, 'Jamon Raza Ibérica', 'Jamon del bueno pata de negro del bueno de los que estan buenos sabes?', 150.00, 4, 'No', '2025-03-02', 'jamonpatanegra.png'),
(3, 1, 'Jamon Iberico 50', 'De bellota como a mi me gustan si si que bueno tio diooooos', 90.00, 6, 'Si', '2025-03-02', 'jamon-de-bellota-iberico-50.webp'),
(4, 2, 'Jamón Serrano de San Rafael 12 meses', 'Jamón Serrano de San Rafael de 12 meses de curación sin duda buen cebo buena caza buena comida', 5.00, 0, 'No', '2025-03-02', 'serranosanrafael.png'),
(5, 3, 'Jamon Iberico de Cebo 50', 'Jamon Iberico sin mas que mas puedes pedir lo baratico sale caro', 50.00, 10, 'Si', '2025-03-02', 'iberico50cebo.jpeg'),
(6, 4, 'Jamon de Jabugo Iberico 50', 'Jamon de Jabugo del bueno bueno que buen sitio de jamones', 200.00, 2, 'No', '2025-03-02', 'png-clipart-jabugo-black-iberian-pig-bayonne-ham-jamon-iberico-jamon-food-animal-source-foods-thumbnail.png'),
(7, 6, 'Pavo Virginia de FUD', 'Jamón de Pavo virginia de la empresa FUD de lo bueno bueno no se gasta eh JJAJAJAAJAJ Me cago en to compradmelo perros, to culpa de Perro Xanchez', 1.98, 50, 'SI', '2025-03-02', 'pavofud.png'),
(8, 7, 'Jamon Iberico de Trevelez de 23 meses de curación', 'JOE QUE BUENO TIO DIOOOOOOH DIOOOOOOH ENCIMA 23 como Alonso, creo que me he equivocado', 80.08, 5, 'No', '2025-03-02', 'trevelez_23meses.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `email`, `password`, `rol`) VALUES
(1, 'Admin', 'istrador', 'admin@admin.com', '$2y$04$j1QgW1ea7us9NU8J7XqYVuS9kRpEEqjyKXI/s0IhNzbk9zmhaaV7i', 'admin'),
(2, 'Adrian', 'Martin', 'adrianmv7205@gmail.com', '$2y$04$4V9xgBQcT4UvhoWYFc/M0.bhuMV/BuaX3aY/omIOwrRUW2xOIT5Mu', 'user'),
(3, 'Paco', 'Mer', 'pacomer69@gmail.com', '$2y$04$.ahjRFPIb/OEC8Nxs5Ipo.OHLlM./CLDjsGLBLsg2LsxFQ89xqPyC', 'admin'),
(4, 'Pepe', 'Tear', 'pepetear777@gmail.com', '$2y$04$RVGsi0RxCrcK7hpk3qDi5.LSFoendyiPdIcroaYDfhTkoufgbjhAW', 'admin'),
(5, 'Sara', 'Garzon', 'sgargal07@gmail.com', '$2y$04$TBTdM6jg7sxaOcV/rwcT0OGVvER3NqN7eEGtBdbSBh7ncSLSJPZE6', 'user'),
(6, 'Adrian', 'Martin', 'adrianmv25@outlook.es', '$2y$04$vS.0i2xVCF.pgD.nT5w4LOPjzI.jyEwGvYcGyUR0lYQOORv6//9YW', 'user'),
(7, 'Adrian', 'Martin', 'adrianmv2507@outlook.es', '$2y$04$C6vm.bthT/YjCL/BYupq4eIXJo0zWTJwiNTbvnh2RIXSBfy5GlTci', 'user'),
(8, 'Lorenzo', 'Lopez', 'lorenzolop007@gmail.com', '$2y$04$8knDbQTf93mIdIt0IgQqDOUcwcKthzeqR6bKprn4DMzRW24DD.dvG', 'user'),
(9, 'Juan', 'Garcia', 'juangar88@gmail.com', '$2y$04$/GTU5ucOiLPkz7ZdetzX..02lqOTJLA3tNB0ZzrRe24cKHiC9Ilea', 'user'),
(10, 'Diaz', 'Afan', 'diasoleado22@gmail.com', '$2y$04$7bVbXPHbnnOeOz0471liHuk.V2J39KdqdZWl5YfTFXnyx.4qhOGzW', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `lineas_pedidos`
--
ALTER TABLE `lineas_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_linea_pedido` (`pedido_id`),
  ADD KEY `fk_linea_producto` (`producto_id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_usuario` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_producto_categoria` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `lineas_pedidos`
--
ALTER TABLE `lineas_pedidos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `lineas_pedidos`
--
ALTER TABLE `lineas_pedidos`
  ADD CONSTRAINT `fk_linea_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `fk_linea_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
