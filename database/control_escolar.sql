-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-06-2025 a las 20:58:33
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
-- Base de datos: `control_escolar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `matricula` varchar(10) NOT NULL,
  `dgp` varchar(20) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(50) DEFAULT NULL,
  `apellido_materno` varchar(50) DEFAULT NULL,
  `curp` char(18) DEFAULT NULL,
  `genero` enum('M','F','O') NOT NULL,
  `cura` varchar(50) DEFAULT NULL,
  `correo_institucional` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `generacion` varchar(15) DEFAULT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `tiene_maestria_previa` tinyint(1) NOT NULL DEFAULT 0,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`matricula`, `dgp`, `nombre`, `apellido_paterno`, `apellido_materno`, `curp`, `genero`, `cura`, `correo_institucional`, `fecha_inicio`, `generacion`, `nacionalidad`, `tiene_maestria_previa`, `telefono`) VALUES
('A000001', '290534', 'Pedro', 'Bernal', 'Acuña', 'ODAE######RBNKEXG', 'M', 'CURAvmmpQ', 'pbernal@unac.edu.mx', '2025-04-13', '2025-1', 'Mexicana', 0, '312123456789'),
('A000002', '293605', 'Arturo', 'Solorio', 'Barragán', 'EKIC######OSJSOWQ', 'M', 'CURAjEQus', NULL, '2024-08-16', '2025-2', 'Mexicana', 0, NULL),
('A000003', '674333', 'Socorro', 'Miranda', 'Alvarado', 'EYVN######XBWCTWI', 'F', 'CURARwBTl', NULL, '2024-04-25', '2025-2', 'Mexicana', 0, NULL),
('A000004', '675611', 'José Luis', 'Guillen', 'Alva', 'CCPP######YPDZJZN', 'M', 'CURAhOyra', NULL, '2025-02-13', '2025-2', 'Mexicana', 1, NULL),
('A000005', '674333', 'Noelia', 'Cardona', 'Muñiz', 'KZMS######AZAKPCA', 'F', 'CURAPOthz', NULL, '2023-07-04', '2025-3', 'Mexicana', 0, NULL),
('A000006', '290534', 'Alejandro', 'Velasco', 'Acuña', 'ODAE86677RBNKEXG', 'M', 'CURAvmmpQ', NULL, '2025-04-13', '2025-1', 'Mexicana', 0, NULL),
('DG345', '290718', 'Luis', 'Garcia', 'Fernandez', '456Y7UJ8K', 'M', 'CRTVBYNUM', NULL, '2025-06-11', '2025-2', 'Mexicana', 0, NULL),
('JJ1048', '621347', 'José', 'Juarez', 'Lopez', 'JSLO882782787CDM', 'M', NULL, NULL, '2025-06-04', NULL, 'Mexicana', 0, NULL),
('LF9617', '290718', 'Ana', 'Estrada', 'Mondragón', 'JHFJ3883E8BD32', 'F', NULL, NULL, '2025-06-11', NULL, 'Mexicana', 0, NULL),
('XJ6543', '621311', 'Ximena', 'Juarez', 'Garibay', NULL, 'F', NULL, NULL, '2025-08-30', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_info_personal`
--

CREATE TABLE `alumno_info_personal` (
  `matricula` varchar(10) NOT NULL,
  `pais_nacimiento` varchar(100) DEFAULT NULL,
  `estado_ciudad_nacimiento` varchar(100) DEFAULT NULL,
  `nacionalidad` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `correo_personal` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_info_personal`
--

INSERT INTO `alumno_info_personal` (`matricula`, `pais_nacimiento`, `estado_ciudad_nacimiento`, `nacionalidad`, `fecha_nacimiento`, `correo_personal`, `telefono`) VALUES
('A000001', 'México', 'Oaxaca', 'Mexicana', '1997-11-12', 'josefinaalcaraz@hotmail.com', '(018)391-2431x66306'),
('A000002', 'México', 'Guerrero', 'Mexicana', '1996-03-14', 'abrilnunez@yahoo.com', '(311)786-8764x7366'),
('A000003', 'México', 'Durango', 'Mexicana', '1990-11-10', 'vcamacho@gmail.com', '645.889.0932'),
('A000004', 'México', 'Coahuila de Zaragoza', 'Mexicana', '1999-07-18', 'venegasmicaela@yahoo.com', '1-770-946-6410'),
('A000005', 'México', 'Aguascalientes', 'Mexicana', '2002-06-20', 'modestoibarra@escobar-juarez.org', '1-677-952-1377x95142'),
('A000006', 'México', 'Oaxaca', 'Mexicana', '1997-11-12', 'josefinaalcaraz@hotmail.com', '(018)391-2431x66306'),
('DG345', '', '', 'Mexicana', NULL, '', ''),
('JJ1048', NULL, NULL, 'Mexicana', NULL, NULL, NULL),
('LF9617', NULL, NULL, 'Mexicana', NULL, NULL, NULL),
('XJ6543', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_laboral`
--

CREATE TABLE `alumno_laboral` (
  `matricula` varchar(10) NOT NULL,
  `nombre_empresa` varchar(255) DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `area_departamento` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo_corporativo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_laboral`
--

INSERT INTO `alumno_laboral` (`matricula`, `nombre_empresa`, `puesto`, `area_departamento`, `telefono`, `correo_corporativo`) VALUES
('A000001', 'Irizarry-Sanches y Asociados', 'Theatre stage manager', 'Salud', '586-651-2131x48994', 'peraleselvira@industrias.com'),
('A000002', 'Valadez S.A. de C.V.', 'Medical sales representative', 'Educación', '(609)928-0345', 'daliavasquez@maya.net'),
('A000003', 'Vásquez y Maldonado e Hijos', 'Building services engineer', 'TI', '+43(4)2863381612', 'egallegos@despacho.com'),
('A000004', 'Carrasco, Nava y Ontiveros', 'Research officer, trade union', 'Educación', '1-246-658-7029', 'estebantrevino@despacho.com'),
('A000005', 'Badillo-Burgos S.C.', 'Orthoptist', 'Educación', '568.992.4179x581', 'garciacamila@arias.org'),
('A000006', 'Irizarry-Sanches y Asociados', 'Theatre stage manager', 'Salud', '586-651-2131x48994', 'peraleselvira@industrias.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_laboral_ubicacion`
--

CREATE TABLE `alumno_laboral_ubicacion` (
  `matricula` varchar(10) NOT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `estado_ciudad` varchar(100) DEFAULT NULL,
  `colonia` varchar(100) DEFAULT NULL,
  `calle` varchar(100) DEFAULT NULL,
  `num_interno` varchar(10) DEFAULT NULL,
  `num_externo` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_laboral_ubicacion`
--

INSERT INTO `alumno_laboral_ubicacion` (`matricula`, `pais`, `estado_ciudad`, `colonia`, `calle`, `num_interno`, `num_externo`) VALUES
('A000001', 'México', 'Colima', 'Viaducto Guerra', 'Diagonal Carrasco 085 510', '7', '63'),
('A000002', 'México', 'Durango', 'Ampliación Sur Perea', 'Circunvalación Negrete 937 111', '7', '31'),
('A000003', 'México', 'Hidalgo', 'Circunvalación Norte Espinal', 'Diagonal Sonora 411 Interior 709', '4', '66'),
('A000004', 'México', 'Tabasco', 'Calle Bañuelos', 'Retorno Ávalos 142 Edif. 732 , Depto. 135', '5', '51'),
('A000005', 'México', 'México', 'Calzada Villalobos', 'Avenida Vargas 460 Edif. 410 , Depto. 550', '5', '47'),
('A000006', 'México', 'Colima', 'Viaducto Guerra', 'Diagonal Carrasco 085 510', '7', '63');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_ubicacion`
--

CREATE TABLE `alumno_ubicacion` (
  `matricula` varchar(10) NOT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `estado_ciudad` varchar(100) DEFAULT NULL,
  `colonia_localidad` varchar(100) DEFAULT NULL,
  `calle` varchar(100) DEFAULT NULL,
  `num_interno` varchar(10) DEFAULT NULL,
  `num_externo` varchar(10) DEFAULT NULL,
  `codigo_postal` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_ubicacion`
--

INSERT INTO `alumno_ubicacion` (`matricula`, `pais`, `estado_ciudad`, `colonia_localidad`, `calle`, `num_interno`, `num_externo`, `codigo_postal`) VALUES
('A000001', 'México', 'Guerrero', 'Cerrada Sur Anaya', 'Diagonal San Marino 452 Edif. 938 , Depto. 879', '7', '93', NULL),
('A000002', 'México', 'Hidalgo', 'Boulevard Norte Cabán', 'Cerrada Sur Ybarra 798 251', '8', '92', NULL),
('A000003', 'México', 'Baja California', 'Circuito Nueva Zelandia', 'Calle de la Rosa 999 Edif. 029 , Depto. 367', '1', '51', NULL),
('A000004', 'México', 'Morelos', 'Avenida Sur Barraza', 'Callejón México 526 Interior 063', '5', '97', NULL),
('A000005', 'México', 'Tabasco', 'Continuación Sur Pedraza', 'Corredor Berríos 221 321', '1', '47', NULL),
('A000006', 'México', 'Guerrero', 'Cerrada Sur Anaya', 'Diagonal San Marino 452 Edif. 938 , Depto. 879', '7', '93', NULL),
('DG345', '', '', '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `antecedente_academico`
--

CREATE TABLE `antecedente_academico` (
  `matricula` varchar(10) NOT NULL,
  `nivel_educativo_anterior` varchar(100) DEFAULT NULL,
  `nombre_institucion` varchar(255) DEFAULT NULL,
  `ciudad_institucion` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `antecedente_academico`
--

INSERT INTO `antecedente_academico` (`matricula`, `nivel_educativo_anterior`, `nombre_institucion`, `ciudad_institucion`, `fecha_inicio`, `fecha_fin`) VALUES
('A000001', 'Bachillerato', 'Moreno, Grijalva y Padilla', 'San Reynaldo los altos', '2019-10-28', '2020-07-21'),
('A000002', 'Licenciatura', 'Grupo Becerra-Ibarra', 'San Josefina los altos', '2019-11-23', '2023-06-09'),
('A000003', 'Licenciatura', 'Collado-Zelaya', 'San Víctor los altos', '2022-01-29', '2024-03-26'),
('A000004', 'Licenciatura', 'Mondragón-Prado e Hijos', 'Vieja Burundi', '2022-03-17', '2023-05-26'),
('A000005', 'Bachillerato', 'Trujillo, Cazares y Altamirano', 'San Irene los altos', '2020-10-17', '2023-10-28'),
('A000006', 'Bachillerato', 'Moreno, Grijalva y Padilla', 'San Reynaldo los altos', '2019-10-28', '2020-07-21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `costospornivel`
--

CREATE TABLE `costospornivel` (
  `id_costo_nivel` int(11) NOT NULL,
  `nivel_educativo` enum('Especialidad','Licenciatura','Maestria','Doctorado') NOT NULL,
  `costo_inscripcion_std` decimal(10,2) NOT NULL,
  `costo_colegiatura_std` decimal(10,2) NOT NULL,
  `costo_reinscripcion_std` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `costospornivel`
--

INSERT INTO `costospornivel` (`id_costo_nivel`, `nivel_educativo`, `costo_inscripcion_std`, `costo_colegiatura_std`, `costo_reinscripcion_std`) VALUES
(1, 'Especialidad', 1500.00, 1200.00, 1500.00),
(2, 'Licenciatura', 1650.00, 1650.00, 1600.00),
(3, 'Maestria', 2500.00, 2200.00, 2500.00),
(4, 'Doctorado', 3500.00, 3200.00, 3000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_alumno`
--

CREATE TABLE `documentos_alumno` (
  `matricula` varchar(10) NOT NULL,
  `acta_nacimiento` text DEFAULT NULL,
  `curp_doc` text DEFAULT NULL,
  `certificado_estudios` text DEFAULT NULL,
  `titulo_universitario` text DEFAULT NULL,
  `comprobante_domicilio` text DEFAULT NULL,
  `carta_otem` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documentos_alumno`
--

INSERT INTO `documentos_alumno` (`matricula`, `acta_nacimiento`, `curp_doc`, `certificado_estudios`, `titulo_universitario`, `comprobante_domicilio`, `carta_otem`) VALUES
('A000001', 'acta_A000001.pdf', 'curp_A000001.pdf', 'cert_A000001.pdf', 'titulo_A000001.pdf', 'domicilio_A000001.pdf', 'otem_A000001.pdf'),
('A000002', 'acta_A000002.pdf', 'curp_A000002.pdf', 'cert_A000002.pdf', 'titulo_A000002.pdf', 'domicilio_A000002.pdf', 'otem_A000002.pdf'),
('A000003', 'acta_A000003.pdf', 'curp_A000003.pdf', 'cert_A000003.pdf', 'titulo_A000003.pdf', 'domicilio_A000003.pdf', 'otem_A000003.pdf'),
('A000004', 'acta_A000004.pdf', 'curp_A000004.pdf', 'cert_A000004.pdf', 'titulo_A000004.pdf', 'domicilio_A000004.pdf', 'otem_A000004.pdf'),
('A000005', 'acta_A000005.pdf', 'curp_A000005.pdf', 'cert_A000005.pdf', 'titulo_A000005.pdf', 'domicilio_A000005.pdf', 'otem_A000005.pdf'),
('A000006', 'acta_A000001.pdf', 'curp_A000001.pdf', 'cert_A000001.pdf', 'titulo_A000001.pdf', 'domicilio_A000001.pdf', 'otem_A000001.pdf'),
('DG345', '', '', '', '', '', ''),
('JJ1048', NULL, NULL, NULL, NULL, NULL, NULL),
('LF9617', NULL, NULL, NULL, NULL, NULL, NULL),
('XJ6543', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id_historial` int(11) NOT NULL,
  `id_pago` int(11) NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_pago` date NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_pagos`
--

INSERT INTO `historial_pagos` (`id_historial`, `id_pago`, `matricula`, `monto`, `fecha_pago`, `metodo_pago`, `referencia`, `fecha_registro`) VALUES
(12, 284, 'JJ1048', 1650.00, '2025-06-18', 'Transferencia', '123232', '2025-06-18 18:00:41'),
(18, 338, 'LF9617', 1500.00, '2025-06-19', 'Efectivo', '123', '2025-06-19 15:28:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripcion_alumno`
--

CREATE TABLE `inscripcion_alumno` (
  `matricula` varchar(10) NOT NULL,
  `permanencia` varchar(50) DEFAULT NULL,
  `duracion_final_meses` int(11) DEFAULT NULL,
  `promocion_aplicada` varchar(100) DEFAULT NULL,
  `id_promocion` int(11) DEFAULT NULL,
  `modalidad_titulacion` varchar(100) DEFAULT NULL,
  `ciclo_inicio` varchar(10) DEFAULT NULL,
  `ciclo_fin` varchar(10) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT curdate(),
  `modalidad_alumno` varchar(50) DEFAULT NULL,
  `estatus_alumno` enum('Activo','Irregular','Baja','Egresado','Suspendido') NOT NULL DEFAULT 'Activo' COMMENT 'Estatus actual del alumno en la inscripción',
  `fecha_estatus` date DEFAULT curdate(),
  `ciclo_finalizado` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripcion_alumno`
--

INSERT INTO `inscripcion_alumno` (`matricula`, `permanencia`, `duracion_final_meses`, `promocion_aplicada`, `id_promocion`, `modalidad_titulacion`, `ciclo_inicio`, `ciclo_fin`, `fecha_inscripcion`, `modalidad_alumno`, `estatus_alumno`, `fecha_estatus`, `ciclo_finalizado`) VALUES
('A000001', '1 año', NULL, 'archivo regional en red', NULL, 'Tesina', '2025-1', '2026-1', '2025-05-30', 'Mixta', 'Activo', '2025-06-04', NULL),
('A000002', '3 años', NULL, 'política cliente-servidor expandido', NULL, 'Tesis', '2024-2', '2025-1', '2025-05-30', 'Mixta', 'Egresado', '2025-06-04', '2025-06-26'),
('A000003', '2 años', NULL, 'base del conocimiento tolerancia cero de primera línea', NULL, 'Tesis', '2025-2', '2026-3', '2025-05-30', 'Mixta', 'Activo', '2025-06-04', NULL),
('A000004', '3 años', NULL, 'arquitectura estática virtual', NULL, 'Curso', '2025-2', '2026-2', '2025-05-30', 'Mixta', 'Egresado', '2025-06-04', '2025-06-26'),
('A000005', '3 años', NULL, 'desafío holística administrado', NULL, 'Tesina', '2025-3', '2026-3', '2025-05-30', 'Mixta', 'Activo', '2025-06-04', NULL),
('A000006', '1 año', NULL, 'archivo regional en red', NULL, 'Tesina', '2025-1', '2026-1', '2025-05-30', 'Mixta', 'Activo', '2025-06-04', NULL),
('DG345', '', NULL, '', NULL, '', '2025-2', NULL, '2025-06-12', 'Mixta', 'Activo', '2025-06-12', NULL),
('JJ1048', NULL, NULL, NULL, NULL, NULL, '2025-2', NULL, '2025-06-04', 'Mixta', 'Activo', '2025-06-04', NULL),
('LF9617', NULL, NULL, NULL, NULL, NULL, '2025-2', NULL, '2025-06-04', 'Mixta', 'Activo', '2025-06-04', NULL),
('XJ6543', NULL, NULL, NULL, NULL, NULL, '2025-2', NULL, '2025-06-24', 'en_linea', 'Activo', '2025-06-24', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institucion`
--

CREATE TABLE `institucion` (
  `id_institucion` char(6) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `institucion`
--

INSERT INTO `institucion` (`id_institucion`, `nombre`) VALUES
('060081', 'INSTITUTO UNIVERSITARIO DE LAS AMERICAS Y EL CARIBE'),
('060082', 'UNIVERSIDAD UCCEG'),
('060092', 'UNIVERSIDAD IUAC DE MÉXICO'),
('060093', 'UNIVERSIDAD DE LAS AMERICAS Y EL CARIBE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_alumno`
--

CREATE TABLE `materias_alumno` (
  `id` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `clave_materia` varchar(20) NOT NULL,
  `nombre_materia` varchar(100) DEFAULT NULL,
  `cuatrimestre` varchar(7) DEFAULT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL,
  `aprobada` tinyint(1) DEFAULT 0,
  `fecha_registro` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_alumno`
--

INSERT INTO `materias_alumno` (`id`, `matricula`, `clave_materia`, `nombre_materia`, `cuatrimestre`, `calificacion`, `aprobada`, `fecha_registro`) VALUES
(1, 'A000001', 'MAT101', 'Álgebra', '2023-1', 90.00, 1, '2023-02-10'),
(2, 'A000001', 'MAT102', 'Cálculo', '2023-1', 85.00, 1, '2023-02-15'),
(3, 'A000001', 'MAT103', 'Estadística', '2023-2', 78.00, 1, '2023-06-01'),
(4, 'A000001', 'MAT104', 'Lógica', '2023-2', 92.00, 1, '2023-06-10'),
(5, 'A000001', 'MAT105', 'Probabilidad', '2023-3', 88.00, 1, '2023-10-05'),
(6, 'A000002', 'ADM201', 'Contabilidad', '2023-1', 70.00, 1, '2023-02-10'),
(7, 'A000002', 'ADM202', 'Finanzas', '2023-1', 55.00, 1, '2023-02-20'),
(8, 'A000002', 'ADM203', 'Marketing', '2023-2', 60.00, 1, '2023-06-15'),
(9, 'A000002', 'ADM204', 'Ventas', '2023-2', 75.00, 1, '2023-06-20'),
(10, 'A000002', 'ADM205', 'Negociación', '2023-3', 80.00, 1, '2023-10-02'),
(11, 'A000003', 'PSI101', 'Psicología Básica', '2023-1', 68.00, 1, '2023-01-10'),
(12, 'A000003', 'PSI102', 'Neurociencia', '2023-2', 88.00, 1, '2023-05-15'),
(13, 'A000003', 'PSI103', 'Ética Profesional', '2023-3', 92.00, 1, '2023-09-30'),
(14, 'A000003', 'PSI104', 'Desarrollo Humano', '2024-1', 85.00, 0, '2024-01-18'),
(15, 'A000003', 'PSI105', 'Terapias Cognitivas', '2024-2', 79.00, 1, '2024-05-05'),
(16, 'A000004', 'ING101', 'Programación I', '2023-1', 91.00, 1, '2023-02-01'),
(17, 'A000004', 'ING102', 'Estructuras de Datos', '2023-2', 67.00, 1, '2023-06-10'),
(18, 'A000004', 'ING103', 'Bases de Datos', '2023-3', 45.00, 0, '2023-10-10'),
(19, 'A000004', 'ING104', 'Redes I', '2024-1', 70.00, 1, '2024-01-20'),
(20, 'A000004', 'ING105', 'Ingeniería de Software', '2024-2', 80.00, 1, '2024-05-20'),
(21, 'A000005', 'EDU101', 'Pedagogía', '2023-1', 95.00, 1, '2023-01-05'),
(22, 'A000005', 'EDU102', 'Didáctica', '2023-2', 89.00, 1, '2023-06-05'),
(23, 'A000005', 'EDU103', 'Evaluación Educativa', '2023-3', 90.00, 1, '2023-10-05'),
(24, 'A000005', 'EDU104', 'Planeación', '2024-1', 94.00, 1, '2024-01-10'),
(25, 'A000005', 'EDU105', 'Tecnología Educativa', '2024-2', 91.00, 1, '2024-05-08'),
(26, 'DG345', 'MAT101', 'Álgebra I', 'INSERT ', 9.00, 1, '2024-01-15'),
(27, 'DG345', 'FIS102', 'Física Básica', '2024-2', 8.50, 0, '2024-01-18'),
(28, 'DG345', 'QUI103', 'Química General', '2024-2', 6.90, 0, '2024-05-10'),
(29, 'DG345', 'HIS104', 'Historia Contemporánea', '2024-2', 7.50, 1, '2024-05-18'),
(30, 'DG345', 'BIO105', 'Biología Molecular', '2024-2', 9.20, 1, '2024-09-20'),
(31, 'LF9617', 'MAT101', 'Álgebra I', '2024-1', 8.70, 0, '2024-01-12'),
(32, 'LF9617', 'COM106', 'Comunicación Oral', '2024-1', 9.10, 0, '2024-01-17'),
(33, 'LF9617', 'SOC107', 'Sociología', '2024-1', 5.80, 0, '2024-05-14'),
(34, 'LF9617', 'FIL108', 'Filosofía Moderna', '2024-1', 7.30, 1, '2024-09-01'),
(35, 'LF9617', 'PSI109', 'Psicología Educativa', '2024-1', 9.00, 1, '2024-09-12'),
(36, 'XJ6543', 'DER110', 'Derecho Constitucional', '2024-2', 6.50, 0, '2024-01-10'),
(37, 'XJ6543', 'DER111', 'Derecho Penal I', '2024-2', 8.30, 1, '2024-05-15'),
(38, 'XJ6543', 'DER112', 'Derecho Civil', '2024-2', 7.90, 1, '2024-05-25'),
(39, 'XJ6543', 'DER113', 'Derecho Administrativo', '2024-2', 8.20, 1, '2024-09-05'),
(40, 'XJ6543', 'DER114', 'Ética Profesional', '2024-2', 8.70, 1, '2024-09-16'),
(41, 'JJ1048', 'EDU115', 'Teorías del Aprendizaje', '2024-2', 8.60, 1, '2024-01-22'),
(42, 'JJ1048', 'EDU116', 'Didáctica General', '2024-2', 7.80, 1, '2024-05-09'),
(43, 'JJ1048', 'EDU117', 'Planeación Educativa', '2024-2', 8.40, 1, '2024-05-19'),
(44, 'JJ1048', 'EDU118', 'Evaluación Educativa', '2024-2', 9.00, 1, '2024-09-11'),
(45, 'JJ1048', 'EDU119', 'Gestión Escolar', '2024-2', 6.50, 0, '2024-09-21'),
(46, 'A000006', 'ADM120', 'Administración I', '2024-2', 9.50, 1, '2024-01-05'),
(47, 'A000006', 'ADM121', 'Contabilidad Básica', '2024-2', 8.90, 1, '2024-01-20'),
(48, 'A000006', 'ADM122', 'Mercadotecnia', '2024-2', 7.50, 1, '2024-05-10'),
(49, 'A000006', 'ADM123', 'Finanzas I', '2024-3', 6.20, 0, '2024-05-15'),
(50, 'A000006', 'ADM124', 'Comportamiento Organizacional', '2024-3', 8.00, 1, '2024-09-02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_plan`
--

CREATE TABLE `materias_plan` (
  `id` int(11) NOT NULL,
  `programa` varchar(100) NOT NULL,
  `total_materias` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planpagos`
--

CREATE TABLE `planpagos` (
  `id_pago` int(11) NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `monto_regular` decimal(10,2) NOT NULL,
  `monto_pago_puntual` decimal(10,2) DEFAULT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado_pago` enum('Pendiente','Pagado','Vencido','Cancelado') NOT NULL DEFAULT 'Pendiente',
  `fecha_pago` date DEFAULT NULL,
  `monto_pagado` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia_pago` varchar(100) DEFAULT NULL,
  `tipo_pago` enum('Inscripción','Mensualidad','Reinscripción','Otro') NOT NULL DEFAULT 'Mensualidad',
  `numero_mensualidad` int(11) DEFAULT NULL,
  `generado_automatico` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `planpagos`
--

INSERT INTO `planpagos` (`id_pago`, `matricula`, `concepto`, `monto_regular`, `monto_pago_puntual`, `fecha_vencimiento`, `estado_pago`, `fecha_pago`, `monto_pagado`, `metodo_pago`, `referencia_pago`, `tipo_pago`, `numero_mensualidad`, `generado_automatico`) VALUES
(249, 'DG345', 'Inscripción Especialidad - Especialidad en la Enseñanza de Español y Literatura', 1500.00, NULL, '2025-06-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Inscripción', NULL, 1),
(250, 'DG345', 'Mensualidad 1/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-07-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 1, 1),
(251, 'DG345', 'Mensualidad 2/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-08-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 2, 1),
(252, 'DG345', 'Mensualidad 3/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-09-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 3, 1),
(253, 'DG345', 'Mensualidad 4/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-10-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 4, 1),
(254, 'DG345', 'Mensualidad 5/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-11-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 5, 1),
(255, 'DG345', 'Mensualidad 6/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-12-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 6, 1),
(256, 'DG345', 'Mensualidad 7/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-01-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 7, 1),
(257, 'DG345', 'Mensualidad 8/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-02-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 8, 1),
(258, 'DG345', 'Mensualidad 9/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-03-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 9, 1),
(259, 'DG345', 'Mensualidad 10/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-04-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 10, 1),
(260, 'DG345', 'Mensualidad 11/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-05-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 11, 1),
(261, 'DG345', 'Mensualidad 12/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-06-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 12, 1),
(262, 'DG345', 'Mensualidad 13/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-07-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 13, 1),
(263, 'DG345', 'Mensualidad 14/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-08-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 14, 1),
(264, 'DG345', 'Mensualidad 15/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-09-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 15, 1),
(265, 'DG345', 'Mensualidad 16/16 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-10-19', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 16, 1),
(284, 'JJ1048', 'Inscripción Licenciatura - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-06-25', 'Pagado', '2025-06-18', 1650.00, 'Transferencia', '123232', 'Inscripción', NULL, 1),
(285, 'JJ1048', 'Mensualidad 1/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-07-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 1, 1),
(286, 'JJ1048', 'Mensualidad 2/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-08-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 2, 1),
(287, 'JJ1048', 'Mensualidad 3/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-09-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 3, 1),
(288, 'JJ1048', 'Mensualidad 4/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-10-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 4, 1),
(289, 'JJ1048', 'Mensualidad 5/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-11-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 5, 1),
(290, 'JJ1048', 'Mensualidad 6/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2025-12-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 6, 1),
(291, 'JJ1048', 'Mensualidad 7/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-01-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 7, 1),
(292, 'JJ1048', 'Mensualidad 8/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-02-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 8, 1),
(293, 'JJ1048', 'Mensualidad 9/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-03-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 9, 1),
(294, 'JJ1048', 'Mensualidad 10/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-04-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 10, 1),
(295, 'JJ1048', 'Mensualidad 11/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-05-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 11, 1),
(296, 'JJ1048', 'Mensualidad 12/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-06-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 12, 1),
(297, 'JJ1048', 'Mensualidad 13/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-07-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 13, 1),
(298, 'JJ1048', 'Mensualidad 14/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-08-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 14, 1),
(299, 'JJ1048', 'Mensualidad 15/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-09-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 15, 1),
(300, 'JJ1048', 'Mensualidad 16/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-10-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 16, 1),
(301, 'JJ1048', 'Mensualidad 17/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-11-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 17, 1),
(302, 'JJ1048', 'Mensualidad 18/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2026-12-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 18, 1),
(303, 'JJ1048', 'Mensualidad 19/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-01-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 19, 1),
(304, 'JJ1048', 'Mensualidad 20/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-02-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 20, 1),
(305, 'JJ1048', 'Mensualidad 21/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-03-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 21, 1),
(306, 'JJ1048', 'Mensualidad 22/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-04-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 22, 1),
(307, 'JJ1048', 'Mensualidad 23/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-05-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 23, 1),
(308, 'JJ1048', 'Mensualidad 24/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-06-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 24, 1),
(309, 'JJ1048', 'Mensualidad 25/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-07-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 25, 1),
(310, 'JJ1048', 'Mensualidad 26/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-08-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 26, 1),
(311, 'JJ1048', 'Mensualidad 27/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-09-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 27, 1),
(312, 'JJ1048', 'Mensualidad 28/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-10-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 28, 1),
(313, 'JJ1048', 'Mensualidad 29/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-11-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 29, 1),
(314, 'JJ1048', 'Mensualidad 30/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2027-12-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 30, 1),
(315, 'JJ1048', 'Mensualidad 31/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2028-01-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 31, 1),
(316, 'JJ1048', 'Mensualidad 32/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2028-02-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 32, 1),
(317, 'JJ1048', 'Mensualidad 33/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2028-03-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 33, 1),
(318, 'JJ1048', 'Mensualidad 34/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2028-04-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 34, 1),
(319, 'JJ1048', 'Mensualidad 35/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2028-05-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 35, 1),
(320, 'JJ1048', 'Mensualidad 36/36 - Licenciatura en Contaduría Pública y Finanzas', 1650.00, NULL, '2028-06-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 36, 1),
(321, 'JJ1048', 'Reinscripción Licenciatura - Licenciatura en Contaduría Pública y Finanzas', 1600.00, NULL, '2026-06-18', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(338, 'LF9617', 'Inscripción Especialidad - Especialidad en la Enseñanza de Español y Literatura', 1500.00, NULL, '2025-06-27', 'Pagado', '2025-06-19', 1500.00, 'Efectivo', '123', 'Inscripción', NULL, 1),
(339, 'LF9617', 'Mensualidad 1/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-07-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 1, 1),
(340, 'LF9617', 'Mensualidad 2/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-08-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 2, 1),
(341, 'LF9617', 'Mensualidad 3/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-09-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 3, 1),
(342, 'LF9617', 'Mensualidad 4/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-10-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 4, 1),
(343, 'LF9617', 'Mensualidad 5/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-11-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 5, 1),
(344, 'LF9617', 'Mensualidad 6/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2025-12-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 6, 1),
(345, 'LF9617', 'Mensualidad 7/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-01-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 7, 1),
(346, 'LF9617', 'Mensualidad 8/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-02-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 8, 1),
(347, 'LF9617', 'Mensualidad 9/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-03-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 9, 1),
(348, 'LF9617', 'Mensualidad 10/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-04-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 10, 1),
(349, 'LF9617', 'Mensualidad 11/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-05-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 11, 1),
(350, 'LF9617', 'Mensualidad 12/12 - Especialidad en la Enseñanza de Español y Literatura', 1200.00, NULL, '2026-06-20', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 12, 1),
(383, 'A000004', 'Inscripción Doctorado - Doctorado en Alta Dirección y Políticas Educativas', 3500.00, NULL, '2025-06-30', 'Pendiente', NULL, NULL, NULL, NULL, 'Inscripción', NULL, 1),
(384, 'A000004', 'Mensualidad 1/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2025-07-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 1, 1),
(385, 'A000004', 'Mensualidad 2/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2025-08-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 2, 1),
(386, 'A000004', 'Mensualidad 3/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2025-09-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 3, 1),
(387, 'A000004', 'Mensualidad 4/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2025-10-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 4, 1),
(388, 'A000004', 'Mensualidad 5/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2025-11-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 5, 1),
(389, 'A000004', 'Mensualidad 6/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2025-12-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 6, 1),
(390, 'A000004', 'Mensualidad 7/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-01-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 7, 1),
(391, 'A000004', 'Mensualidad 8/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-02-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 8, 1),
(392, 'A000004', 'Mensualidad 9/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-03-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 9, 1),
(393, 'A000004', 'Mensualidad 10/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-04-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 10, 1),
(394, 'A000004', 'Mensualidad 11/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-05-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 11, 1),
(395, 'A000004', 'Mensualidad 12/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-06-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 12, 1),
(396, 'A000004', 'Mensualidad 13/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-07-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 13, 1),
(397, 'A000004', 'Mensualidad 14/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-08-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 14, 1),
(398, 'A000004', 'Mensualidad 15/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-09-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 15, 1),
(399, 'A000004', 'Mensualidad 16/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-10-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 16, 1),
(400, 'A000004', 'Mensualidad 17/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-11-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 17, 1),
(401, 'A000004', 'Mensualidad 18/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2026-12-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 18, 1),
(402, 'A000004', 'Mensualidad 19/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-01-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 19, 1),
(403, 'A000004', 'Mensualidad 20/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-02-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 20, 1),
(404, 'A000004', 'Mensualidad 21/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-03-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 21, 1),
(405, 'A000004', 'Mensualidad 22/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-04-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 22, 1),
(406, 'A000004', 'Mensualidad 23/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-05-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 23, 1),
(407, 'A000004', 'Mensualidad 24/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-06-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 24, 1),
(408, 'A000004', 'Mensualidad 25/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-07-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 25, 1),
(409, 'A000004', 'Mensualidad 26/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-08-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 26, 1),
(410, 'A000004', 'Mensualidad 27/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-09-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 27, 1),
(411, 'A000004', 'Mensualidad 28/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-10-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 28, 1),
(412, 'A000004', 'Mensualidad 29/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-11-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 29, 1),
(413, 'A000004', 'Mensualidad 30/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2027-12-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 30, 1),
(414, 'A000004', 'Mensualidad 31/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2028-01-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 31, 1),
(415, 'A000004', 'Mensualidad 32/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2028-02-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 32, 1),
(416, 'A000004', 'Mensualidad 33/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2028-03-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 33, 1),
(417, 'A000004', 'Mensualidad 34/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2028-04-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 34, 1),
(418, 'A000004', 'Mensualidad 35/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2028-05-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 35, 1),
(419, 'A000004', 'Mensualidad 36/36 - Doctorado en Alta Dirección y Políticas Educativas', 3200.00, NULL, '2028-06-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 36, 1),
(420, 'A000004', 'Reinscripción Doctorado - Doctorado en Alta Dirección y Políticas Educativas', 3000.00, NULL, '2026-06-23', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(519, 'A000005', 'Inscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-07-03', 'Pendiente', NULL, NULL, NULL, NULL, 'Inscripción', NULL, 1),
(520, 'A000005', 'Mensualidad 1/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-07-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 1, 1),
(521, 'A000005', 'Mensualidad 2/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-08-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 2, 1),
(522, 'A000005', 'Mensualidad 3/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-09-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 3, 1),
(523, 'A000005', 'Mensualidad 4/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-10-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 4, 1),
(524, 'A000005', 'Mensualidad 5/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-11-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 5, 1),
(525, 'A000005', 'Mensualidad 6/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2025-12-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 6, 1),
(526, 'A000005', 'Mensualidad 7/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-01-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 7, 1),
(527, 'A000005', 'Mensualidad 8/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-02-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 8, 1),
(528, 'A000005', 'Mensualidad 9/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-03-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 9, 1),
(529, 'A000005', 'Mensualidad 10/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-04-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 10, 1),
(530, 'A000005', 'Mensualidad 11/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-05-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 11, 1),
(531, 'A000005', 'Mensualidad 12/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-06-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 12, 1),
(532, 'A000005', 'Mensualidad 13/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-07-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 13, 1),
(533, 'A000005', 'Mensualidad 14/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-08-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 14, 1),
(534, 'A000005', 'Mensualidad 15/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-09-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 15, 1),
(535, 'A000005', 'Mensualidad 16/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-10-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 16, 1),
(536, 'A000005', 'Mensualidad 17/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-11-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 17, 1),
(537, 'A000005', 'Mensualidad 18/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2026-12-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 18, 1),
(538, 'A000005', 'Mensualidad 19/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-01-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 19, 1),
(539, 'A000005', 'Mensualidad 20/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-02-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 20, 1),
(540, 'A000005', 'Mensualidad 21/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-03-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 21, 1),
(541, 'A000005', 'Mensualidad 22/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-04-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 22, 1),
(542, 'A000005', 'Mensualidad 23/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-05-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 23, 1),
(543, 'A000005', 'Mensualidad 24/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-06-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 24, 1),
(544, 'A000005', 'Mensualidad 25/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-07-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 25, 1),
(545, 'A000005', 'Mensualidad 26/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-08-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 26, 1),
(546, 'A000005', 'Mensualidad 27/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-09-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 27, 1),
(547, 'A000005', 'Mensualidad 28/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-10-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 28, 1),
(548, 'A000005', 'Mensualidad 29/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-11-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 29, 1),
(549, 'A000005', 'Mensualidad 30/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2027-12-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 30, 1),
(550, 'A000005', 'Mensualidad 31/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2028-01-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 31, 1),
(551, 'A000005', 'Mensualidad 32/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2028-02-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 32, 1),
(552, 'A000005', 'Mensualidad 33/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2028-03-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 33, 1),
(553, 'A000005', 'Mensualidad 34/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2028-04-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 34, 1),
(554, 'A000005', 'Mensualidad 35/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2028-05-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 35, 1),
(555, 'A000005', 'Mensualidad 36/36 - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 1650.00, NULL, '2028-06-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 36, 1),
(556, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 4)', 1600.00, NULL, '2025-10-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(557, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 8)', 1600.00, NULL, '2026-02-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(558, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 12)', 1600.00, NULL, '2026-06-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(559, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 16)', 1600.00, NULL, '2026-10-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(560, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 20)', 1600.00, NULL, '2027-02-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(561, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 24)', 1600.00, NULL, '2027-06-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(562, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 28)', 1600.00, NULL, '2027-10-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(563, 'A000005', 'Reinscripción Licenciatura - Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa (mes 32)', 1600.00, NULL, '2028-02-26', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(798, 'A000001', 'Inscripción Maestria - Maestría en la Enseñanza de Inglés', 2500.00, NULL, '2025-07-04', 'Pendiente', NULL, NULL, NULL, NULL, 'Inscripción', NULL, 1),
(799, 'A000001', 'Mensualidad 1/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2025-07-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 1, 1),
(800, 'A000001', 'Mensualidad 2/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2025-08-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 2, 1),
(801, 'A000001', 'Mensualidad 3/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2025-09-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 3, 1),
(802, 'A000001', 'Mensualidad 4/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2025-10-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 4, 1),
(803, 'A000001', 'Mensualidad 5/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2025-11-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 5, 1),
(804, 'A000001', 'Mensualidad 6/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2025-12-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 6, 1),
(805, 'A000001', 'Mensualidad 7/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-01-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 7, 1),
(806, 'A000001', 'Mensualidad 8/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-02-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 8, 1),
(807, 'A000001', 'Mensualidad 9/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-03-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 9, 1),
(808, 'A000001', 'Mensualidad 10/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-04-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 10, 1),
(809, 'A000001', 'Mensualidad 11/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-05-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 11, 1),
(810, 'A000001', 'Mensualidad 12/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-06-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 12, 1),
(811, 'A000001', 'Mensualidad 13/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-07-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 13, 1),
(812, 'A000001', 'Mensualidad 14/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-08-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 14, 1),
(813, 'A000001', 'Mensualidad 15/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-09-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 15, 1),
(814, 'A000001', 'Mensualidad 16/16 - Maestría en la Enseñanza de Inglés', 1760.00, NULL, '2026-10-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Mensualidad', 16, 1),
(815, 'A000001', 'Reinscripción Maestria - Maestría en la Enseñanza de Inglés (Cuat. 2)', 2500.00, NULL, '2025-10-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(816, 'A000001', 'Reinscripción Maestria - Maestría en la Enseñanza de Inglés (Cuat. 3)', 2500.00, NULL, '2026-02-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1),
(817, 'A000001', 'Reinscripción Maestria - Maestría en la Enseñanza de Inglés (Cuat. 4)', 2500.00, NULL, '2026-06-27', 'Pendiente', NULL, NULL, NULL, NULL, 'Reinscripción', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa`
--

CREATE TABLE `programa` (
  `dgp` varchar(20) NOT NULL,
  `nombre_programa` varchar(255) DEFAULT NULL,
  `abreviacion` varchar(50) DEFAULT NULL,
  `rvoe` varchar(50) DEFAULT NULL,
  `denominacion_autorizada` varchar(255) DEFAULT NULL,
  `modalidades` varchar(100) DEFAULT NULL,
  `nivel_educativo` varchar(100) DEFAULT NULL,
  `fecha_rvoe` date DEFAULT NULL,
  `id_institucion` char(6) DEFAULT NULL,
  `duracion_meses` int(11) NOT NULL DEFAULT 0,
  `duracion_meses_alt` int(11) DEFAULT NULL,
  `total_materias` int(11) DEFAULT 0,
  `materias_doctorado_maestria` int(11) DEFAULT NULL,
  `materias_doctorado_licenciatura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `programa`
--

INSERT INTO `programa` (`dgp`, `nombre_programa`, `abreviacion`, `rvoe`, `denominacion_autorizada`, `modalidades`, `nivel_educativo`, `fecha_rvoe`, `id_institucion`, `duracion_meses`, `duracion_meses_alt`, `total_materias`, `materias_doctorado_maestria`, `materias_doctorado_licenciatura`) VALUES
('111552', 'Maestría en la Enseñanza de las Matemáticas', 'MEM', '20221187', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 20, NULL, NULL),
('111592', 'Maestría en Seguridad Informática y Gestión de las Tecnologías de la Información', 'MSIGTI', 'SE-015-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060093', 0, NULL, 20, NULL, NULL),
('111631', 'Doctorado en Enseñanza de las Matemáticas ', 'DEM', 'SE-039-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2018-11-12', '060093', 0, NULL, NULL, 12, 18),
('111632', 'Doctorado en Sistemas de Información', 'DSI', 'SE-017-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2018-11-12', '060093', 0, NULL, NULL, 12, 18),
('111721', 'Especialidad en la Enseñanza de las Matemáticas', 'EEM', '20221198', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 16, NULL, NULL),
('130532', 'Maestría en la Enseñanza de las Ciencias Naturales', 'MECN', '20221206', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 20, NULL, NULL),
('130707', 'Especialidad en la Enseñanza de las Ciencias Naturales', 'EECN', '20221196', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 16, NULL, NULL),
('203550', 'Maestría en Atención a la Diversidad y Educación Inclusiva', 'MADEI', 'SE-008-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2021-07-17', '060093', 0, NULL, 20, NULL, NULL),
('207399', 'Licenciatura en Educación Inicial', 'LEI', 'SE-013-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2018-11-12', '060093', 0, NULL, 38, NULL, NULL),
('209601', 'Doctorado en Docencia  y Educación Artística ', 'DDEA', 'SE-010-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2021-07-17', '060093', 0, NULL, NULL, 12, 18),
('223370', 'Licenciatura en Enseñanza de Inglés', 'LEIN', 'SE-006-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2021-07-17', '060093', 0, NULL, 38, NULL, NULL),
('225572', 'Maestría en Moda y Diseño', 'MMD', 'SE-016-2020-ES', 'Universidad de las Américas y el Caribe ', 'Mixta', 'Maestria', '2018-11-12', '060093', 0, NULL, 20, NULL, NULL),
('241557', 'Maestría en Docencia', 'MD', '20221202', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 20, NULL, NULL),
('241603', 'Doctorado en Educación', 'DE', '20221190', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Doctorado', '2018-11-12', '060081', 0, NULL, NULL, 12, 18),
('244759', 'Especialidad en la Enseñanza de Educación Física', 'EEEF', '20221192', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, NULL, NULL, NULL),
('277302', 'Licenciatura en Enseñanza de las Ciencias Sociales', 'LECS', 'SE-051-2021-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2019-11-29', '060093', 0, NULL, 38, NULL, NULL),
('288627', 'Doctorado en Educación', 'DE', '20171092', 'Universitarios UCCEG', 'Mixta', 'Doctorado', '2016-04-19', '060082', 0, NULL, NULL, 12, 18),
('290339', 'Licenciatura en Enseñanza de las Ciencias Naturales', 'LECN', 'SE-052-2021-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2019-11-29', '060093', 0, NULL, 38, NULL, NULL),
('290533', 'Maestría en Enseñanza de la Educación Física', 'MEEF', '20221203', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 20, NULL, NULL),
('290534', 'Maestría en la Enseñanza de Inglés', 'MEI', '20221204', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 9, NULL, NULL),
('290620', 'Doctorado en Educación y Comunicación Social', 'DECS', 'SE-019-2020/2018-11-13', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2019-11-20', '060093', 0, NULL, NULL, 12, 18),
('290718', 'Especialidad en la Enseñanza de Español y Literatura', 'EEEL', '20221193', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 4, NULL, NULL),
('290719', 'Especialidad en la Enseñanza de Inglés', 'EEI', '20221194', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 16, NULL, NULL),
('291510', 'Maestría en Entrenamiento Deportivo con Énfasis en Fútbol', 'MEDEF', 'SE-072-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060093', 0, NULL, 20, NULL, NULL),
('292332', 'Licenciatura en Enseñanza  de Español y Literatura', 'LEEL', 'SE-053-2021-ES', 'Universidad de las Américas y el Caribe', 'Mixto', 'Licenciatura', '2019-11-29', '060093', 0, NULL, 38, NULL, NULL),
('292333', 'Licenciatura en Enseñanza de la Educación Física', 'LEEF', 'SE-073-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2018-11-12', '060093', 0, NULL, 38, NULL, NULL),
('292550', 'Maestría en Docencia Especializada en Educación Media Superior', 'MDEEMS', 'SE-046-2021-ES', 'Universidad IUAC de México', 'Mixta', 'Maestria', '2019-11-29', '060092', 0, NULL, 0, NULL, NULL),
('292559', 'Maestría en Educación Inicial ', 'MEEI', 'SE-014-2020-ES', 'Universidad de las Américas y el Caribe ', 'Mixta', 'Maestria', '2018-11-12', '060093', 0, NULL, 24, NULL, NULL),
('292560', 'Maestría en la Enseñanza del Español y Literatura ', 'MEEL', 'SE-071-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060093', 0, NULL, 24, NULL, NULL),
('292619', 'Doctorado en Educación Media Superior con Énfasis en Pedagogía Docente', 'DEMSEPD', 'SE-047-2021-ES', 'Universidad IUAC de México', 'Mixta', 'Doctorado', '2019-11-29', '060092', 0, NULL, 0, 12, 18),
('292624', 'Doctorado en Educación Física y Actividad Deportiva', 'DEFAD', 'SE-070-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2018-11-12', '060093', 0, NULL, 12, 12, 18),
('293310', 'Licenciatura en Pedagogía con Especialidad en Dirección y Administración Educativa', 'LPEDAE', 'SE-011-2022-ES', 'Universidad IUAC de México', 'Mixta', 'Licenciatura', '2022-05-15', '060092', 0, NULL, 40, NULL, NULL),
('293605', 'Doctorado en Gerencia y Pedagogía Hospitalaria', 'DGPH', 'SE-031-2023-ES', 'Universidad IUAC de México', 'Mixta', 'Doctorado', '2023-08-01', '060092', 0, NULL, 0, 12, 5),
('294304', 'Licenciatura en Psicología Organizacional y Empresarial', 'LPOE', 'SE-024-2022-ES', 'Universidad IUAC de México', 'Mixta', 'Licenciatura', '2021-03-21', '060092', 0, NULL, 40, NULL, NULL),
('294605', 'Doctorado en Psicología Aplicada a la Educación y al Desarrollo Humano', 'DPAEDH', 'SE-014-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2021-05-17', '060093', 0, NULL, 12, 12, 18),
('295613', 'Doctorado en Educación Especial e Inclusión', 'DEEI', 'SE-009-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2021-07-17', '060093', 0, NULL, 12, 12, 18),
('456515', 'Maestría en Administración Hospitalaria', 'MAH', '20221199', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2022-09-29', '060081', 0, NULL, 24, NULL, NULL),
('602592', 'Maestría en Derecho Civil y Familiar', 'MDCF', '20221200', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 24, NULL, NULL),
('607602', 'Doctorado en Administración', 'DA', 'SE-018-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Doctorado', '2018-11-12', '060093', 0, NULL, 12, 12, 18),
('612514', 'Maestría en Derecho Penal', 'MDP', '20221201', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 24, NULL, NULL),
('620538', 'Maestría en Administración y Finanzas', 'MAF', 'SE-020-2020/2018-11-13', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2020-11-20', '060093', 0, NULL, 24, NULL, NULL),
('621311', 'Licenciatura en Administración', 'LA', 'SE-021-2020', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2018-11-29', '060093', 0, NULL, 15, NULL, NULL),
('621347', 'Licenciatura en Contaduría Pública y Finanzas', 'LCPF', 'SE-012-2022-ES', 'Universidad IUAC de México', 'Mixta', 'Licenciatura', '2021-03-26', '060092', 0, NULL, 15, NULL, NULL),
('621587', 'Maestría en Recursos Humanos', 'MRH', '20221188', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 24, NULL, NULL),
('644557', 'Maestría en la Enseñanza de las Ciencias Sociales', 'MECS', '20221186', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 0, NULL, NULL),
('644713', 'Especialidad en la Enseñanza de las Ciencias Sociales', 'EECS', '20221197', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 0, NULL, NULL),
('656612', 'Doctorado en Administración Pública y Políticas Públicas', 'DAPPP', '20221189', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Doctorado', '2018-11-12', '060081', 0, NULL, 12, 12, 18),
('656623', 'Doctorado en Filosofía, Gobierno y Politicas Públicas Ph. D.', 'DFGPP', 'SE-044-2021-ES', 'Universidad IUAC de México', 'Mixta', 'Doctorado', '2019-11-29', '060092', 0, NULL, 12, 12, 18),
('668720', 'Especialidad en Administración Hospitalaria', 'EAH', '20221191', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 0, NULL, NULL),
('671607', 'Doctorado en Política y Gobierno', 'DPG', '20171093', 'Universitarios UCCEG', 'Mixta', 'Doctorado', '2016-04-19', '060082', 0, NULL, 0, 12, 18),
('672559', 'Maestría en Alta Dirección de Instituciones Educativas', 'MADIE', 'SE-022-2020-ES', 'Universidad IUAC de México', 'Mixta', 'Maestría', '2020-10-20', '060092', 0, NULL, 12, NULL, NULL),
('674333', 'Licenciatura en Derecho con Especialidad en Juicios Orales y Justicia Alternativa', 'LDEJOJA', 'SE-045-2021-ES', 'Universidad IUAC de México', 'Mixta', 'Licenciatura', '2020-03-06', '060092', 0, NULL, 15, NULL, NULL),
('675611', 'Doctorado en Alta Dirección y Políticas Educativas', 'DADPE', 'SE-042-2021-ES', 'Universidad IUAC de México', 'Mixta', 'Doctorado', '2019-11-29', '060092', 0, NULL, NULL, 4, 5),
('678575', 'Maestría en Administración y Gestión en los Servicios de Enfermería', 'MAGSE', 'SE-023-2022-ES', 'Universidad IUAC de México', 'Mixta', 'Maestria', '2021-03-21', '060092', 0, NULL, 0, NULL, NULL),
('688542', 'Maestría en Cooperación Internacional y Ayuda Humanitaria', 'MCIAH', 'SE-049-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2023-06-19', '060093', 0, NULL, 24, NULL, NULL),
('710350', 'Licenciatura en la Enseñanza de las Artes ', 'LEA', 'SE-012-2020-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Licenciatura', '2018-11-12', '060093', 0, NULL, 0, NULL, NULL),
('710525', 'Maestría en la Enseñanza de las Artes', 'MEA', '20221205', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Maestria', '2018-11-12', '060081', 0, NULL, 24, NULL, NULL),
('710704', 'Especialidad en la Enseñanza de las Artes', 'EEA', '20221195', 'Instituto Universitario de las Américas y el Caribe', 'Mixta', 'Especialidad', '2022-09-29', '060081', 0, NULL, 0, NULL, NULL),
('712503', 'Maestría en Educación Artística', 'MEDUA', 'SE-007-2022-ES', 'Universidad de las Américas y el Caribe', 'Mixta', 'Maestria', '2021-07-17', '060093', 0, NULL, 24, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `nombre_promo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `inscripcion_promo` decimal(10,2) DEFAULT NULL,
  `colegiatura_mes1_promo` decimal(10,2) DEFAULT NULL,
  `colegiatura_desc_porc` decimal(5,2) DEFAULT NULL,
  `reinscripcion_promo` decimal(10,2) DEFAULT NULL,
  `reinscripcion_pago_puntual` decimal(10,2) DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promocion_antigua`
--

CREATE TABLE `promocion_antigua` (
  `id` int(11) NOT NULL,
  `nombre_promocion` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo_inscripcion` decimal(10,2) NOT NULL,
  `costo_colegiatura` decimal(10,2) NOT NULL,
  `costo_reinscripcion` decimal(10,2) NOT NULL,
  `descuento_inscripcion` decimal(5,2) DEFAULT 0.00,
  `descuento_colegiatura` decimal(5,2) DEFAULT 0.00,
  `descuento_reinscripcion` decimal(5,2) DEFAULT 0.00,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promocion_antigua`
--

INSERT INTO `promocion_antigua` (`id`, `nombre_promocion`, `descripcion`, `costo_inscripcion`, `costo_colegiatura`, `costo_reinscripcion`, `descuento_inscripcion`, `descuento_colegiatura`, `descuento_reinscripcion`, `fecha_inicio`, `fecha_fin`, `activa`, `creado_en`) VALUES
(1, 'Promoción de Invierno 2024', NULL, 5000.00, 3000.00, 2500.00, 20.00, 15.50, 10.00, '2024-01-15', '2024-02-15', 1, '2025-06-09 17:46:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `tipo_usuario` enum('marketing','control','finanzas','titulacion','soporte','academico','tesis','alumnos','maestros') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `contrasena`, `tipo_usuario`) VALUES
(0, 'hjuarez@gmail.com', '$2y$10$yq5uPQtY6mlUrMmr2qBmT.3gvOYul6ucjOJgeUDDsBhjQeGGHWVEy', 'marketing'),
(0, 'amauri@gmail.com', '$2y$10$otON19RKA/TR6hwBY7ZULe0jrzIfIQjHaYkEq87rDveuJ17FOMc7q', 'control'),
(0, 'jazz@gmail.com', '$2y$10$aCvzZs1HrNuXgtbh.FMNAugNSK2.zkCX7Lrjsf2FnxIsnl58ws3Ba', 'soporte');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`matricula`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD UNIQUE KEY `correo_institucional` (`correo_institucional`),
  ADD KEY `dgp` (`dgp`);

--
-- Indices de la tabla `alumno_info_personal`
--
ALTER TABLE `alumno_info_personal`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `alumno_laboral`
--
ALTER TABLE `alumno_laboral`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `alumno_laboral_ubicacion`
--
ALTER TABLE `alumno_laboral_ubicacion`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `alumno_ubicacion`
--
ALTER TABLE `alumno_ubicacion`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `antecedente_academico`
--
ALTER TABLE `antecedente_academico`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `costospornivel`
--
ALTER TABLE `costospornivel`
  ADD PRIMARY KEY (`id_costo_nivel`),
  ADD UNIQUE KEY `nivel_educativo` (`nivel_educativo`);

--
-- Indices de la tabla `documentos_alumno`
--
ALTER TABLE `documentos_alumno`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_pago` (`id_pago`),
  ADD KEY `matricula` (`matricula`);

--
-- Indices de la tabla `inscripcion_alumno`
--
ALTER TABLE `inscripcion_alumno`
  ADD PRIMARY KEY (`matricula`),
  ADD KEY `fk_inscripcion_promocion` (`id_promocion`);

--
-- Indices de la tabla `institucion`
--
ALTER TABLE `institucion`
  ADD PRIMARY KEY (`id_institucion`);

--
-- Indices de la tabla `materias_alumno`
--
ALTER TABLE `materias_alumno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matricula` (`matricula`);

--
-- Indices de la tabla `materias_plan`
--
ALTER TABLE `materias_plan`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `planpagos`
--
ALTER TABLE `planpagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `fk_pago_matricula` (`matricula`);

--
-- Indices de la tabla `programa`
--
ALTER TABLE `programa`
  ADD PRIMARY KEY (`dgp`),
  ADD KEY `id_institucion` (`id_institucion`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `promocion_antigua`
--
ALTER TABLE `promocion_antigua`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `costospornivel`
--
ALTER TABLE `costospornivel`
  MODIFY `id_costo_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `materias_alumno`
--
ALTER TABLE `materias_alumno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `materias_plan`
--
ALTER TABLE `materias_plan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `planpagos`
--
ALTER TABLE `planpagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=818;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `promocion_antigua`
--
ALTER TABLE `promocion_antigua`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`dgp`) REFERENCES `programa` (`dgp`);

--
-- Filtros para la tabla `alumno_info_personal`
--
ALTER TABLE `alumno_info_personal`
  ADD CONSTRAINT `alumno_info_personal_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `alumno_laboral`
--
ALTER TABLE `alumno_laboral`
  ADD CONSTRAINT `alumno_laboral_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `alumno_laboral_ubicacion`
--
ALTER TABLE `alumno_laboral_ubicacion`
  ADD CONSTRAINT `alumno_laboral_ubicacion_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `alumno_ubicacion`
--
ALTER TABLE `alumno_ubicacion`
  ADD CONSTRAINT `alumno_ubicacion_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `antecedente_academico`
--
ALTER TABLE `antecedente_academico`
  ADD CONSTRAINT `antecedente_academico_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documentos_alumno`
--
ALTER TABLE `documentos_alumno`
  ADD CONSTRAINT `documentos_alumno_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD CONSTRAINT `historial_pagos_ibfk_1` FOREIGN KEY (`id_pago`) REFERENCES `planpagos` (`id_pago`),
  ADD CONSTRAINT `historial_pagos_ibfk_2` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`);

--
-- Filtros para la tabla `inscripcion_alumno`
--
ALTER TABLE `inscripcion_alumno`
  ADD CONSTRAINT `fk_inscripcion_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`),
  ADD CONSTRAINT `inscripcion_alumno_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias_alumno`
--
ALTER TABLE `materias_alumno`
  ADD CONSTRAINT `materias_alumno_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `alumno` (`matricula`);

--
-- Filtros para la tabla `planpagos`
--
ALTER TABLE `planpagos`
  ADD CONSTRAINT `fk_pago_matricula` FOREIGN KEY (`matricula`) REFERENCES `inscripcion_alumno` (`matricula`) ON DELETE CASCADE;

--
-- Filtros para la tabla `programa`
--
ALTER TABLE `programa`
  ADD CONSTRAINT `programa_ibfk_1` FOREIGN KEY (`id_institucion`) REFERENCES `institucion` (`id_institucion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
