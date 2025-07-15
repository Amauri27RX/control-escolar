<?php
require_once '../conexion.php';

// Consulta para obtener todos los alumnos
$query = "SELECT matricula, nombre, apellido_paterno, apellido_materno FROM alumno ORDER BY apellido_paterno";
$result = $conn->query($query);

// Mostrar mensaje de error si viene de redirección
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alumnos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --white: #ffffff; /* Se define la variable --white con el valor del color blanco */
            --primary: rgba(6, 31, 142, 0.84);
            --primary-dark: #3a56d4;
            --secondary:rgb(72, 8, 115);
            --success: #06d6a0;
            --danger: #ef476f;
            --warning: #ffd166;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #eef2ff 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(67, 97, 238, 0.15);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            background: var(--primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }

        .logo-text span {
            color: var(--secondary);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 22px;
            color: var(--dark);
            margin-bottom: 30px;
            margin-left: 170px
        }

        .page-title i {
            background: var(--primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            display: flex;
            background: white;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }

        .search-box input {
            flex: 1;
            border: none;
            padding: 15px 20px;
            font-size: 16px;
            outline: none;
        }

        .search-box button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            cursor: pointer;
            transition: var(--transition);
            font-size: 18px;
        }

        .search-box button:hover {
            background: var(--primary-dark);
        }

        .filter-sort {
            display: flex;
            gap: 15px;
        }

        .filter-btn, .sort-btn {
            background: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--gray);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
        }

        .filter-btn:hover, .sort-btn:hover {
            background: var(--light);
            color: var(--primary);
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 25px;
            background: var(--gray);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
        }

        .total-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 50px;
            font-weight: 600;
        }

        .table-container {
            overflow-x: auto;
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        thead th {
            background: var(--light-gray);
            color: var(--gray);
            font-weight: 600;
            text-align: left;
            padding: 18px 25px;
            border-bottom: 2px solid var(--light-gray);
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: rgba(67, 97, 238, 0.03);
            transform: translateY(-2px);
        }

        tbody td {
            padding: 18px 25px;
            border-bottom: 1px solid var(--light-gray);
            color: var(--dark);
        }

        .matricula {
            font-weight: 600;
            color: var(--primary);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 14px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.3);
        }

        .btn-secondary {
            background: rgba(114, 9, 183, 0.1);
            color: var(--secondary);
        }

        .btn-secondary:hover {
            background: rgba(114, 9, 183, 0.2);
        }

        .error-message {
            background: rgba(239, 71, 111, 0.1);
            border-left: 4px solid var(--danger);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: fadeIn 0.5s ease;
        }

        .error-message i {
            color: var(--danger);
            font-size: 24px;
        }

        .status-badge.activo {
            background: rgba(6, 214, 160, 0.15);
            color: #06a17c;
            border-radius: 12px;
            padding: 5px;
        }

        .status-badge.irregular {
            background: rgba(255, 193, 7, 0.15);
            color: #b08500;
            border-radius: 12px;
            padding: 5px;
        }

        .status-badge.baja {
            background: rgba(220, 53, 69, 0.15);
            color: #c82333;
            border-radius: 12px;
            padding: 5px;
        }

        .status-badge.egresado {
            background: rgba(0, 123, 255, 0.15);
            color: #0056b3;
            border-radius: 12px;
            padding: 5px;
        }

        .status-badge.suspendido {
            background: rgba(108, 117, 125, 0.15);
            color: #5c636a;
            border-radius: 12px;
            padding: 5px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 8px;
        }

        .pagination button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: white;
            color: var(--gray);
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            transition: var(--transition);
        }

        .pagination button.active, .pagination button:hover {
            background: var(--primary);
            color: white;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            padding: 25px 0;
            color: var(--gray);
            font-size: 14px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .responsive-table {
            display: none;
        }

        @media (max-width: 768px) {
            .desktop-table {
                display: none;
            }
            
            .responsive-table {
                display: block;
            }
            
            .student-card {
                background: white;
                border-radius: var(--border-radius);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                padding: 20px;
                margin-bottom: 20px;
                transition: var(--transition);
            }
            
            .student-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            }
            
            .student-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 15px;
            }
            
            .student-matricula {
                font-weight: 700;
                color: var(--primary);
                font-size: 18px;
            }
            
            .student-name {
                font-size: 18px;
                font-weight: 600;
                margin-bottom: 15px;
            }
            
            .student-actions {
                display: flex;
                gap: 10px;
                margin-top: 15px;
            }
            
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                width: 100%;
            }
            
            .filter-sort {
                justify-content: space-between;
            }
            
            .error-message {
                flex-direction: column;
                text-align: center;
                padding: 15px;
            }
            
            .error-message i {
                margin-bottom: 10px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .highlight {
            background-color: rgba(255, 215, 0, 0.2);
            transition: background-color 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="page-title">
                <i class="fas fa-users"></i>
                <h1>Relacion de alumnos por plan de pago</h1>
            </div>
        </header>

        <div class="controls">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar alumno por nombre o matrícula...">
                <button id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="filter-sort">
                <button class="filter-btn">
                    <i class="fas fa-filter"></i> Filtros
                </button>
                <button class="sort-btn">
                    <i class="fas fa-sort-amount-down"></i> Ordenar
                </button>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Alumnos Registrados</div>
                <div class="total-count"><?= $result->num_rows ?> alumnos</div>
            </div>
            
            <!-- Desktop Table -->
            <div class="desktop-table">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre Completo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                while ($row = $result->fetch_assoc()): 
                                    $matricula = $row['matricula'];
                                    $status = 'activo'; // Valor por defecto

                                    // Consulta para obtener el estatus real del alumno
                                    $estatus_result = $conn->query("SELECT estatus_alumno FROM inscripcion_alumno WHERE matricula = '$matricula' LIMIT 1");
                                    if ($estatus_result && $estatus_row = $estatus_result->fetch_assoc()) {
                                        $status = strtolower(trim($estatus_row['estatus_alumno']));
                                    }
                            ?>

                            <tr>
                                <td class="matricula"><?= htmlspecialchars($row['matricula']) ?></td>
                                <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno']) ?></td>
                                <td>
                                    <span class="status-badge <?= str_replace(' ', '-', $status) ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <?php if ($status === 'activo'): ?>
                                        <a href="plan_pagos_alumno.php?matricula=<?= urlencode($row['matricula']) ?>" class="btn btn-primary">
                                            <i class="fas fa-money-bill-wave"></i> Plan de Pagos
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled title="Alumno con estatus: <?= ucfirst($status) ?>">
                                            <i class="fas fa-money-bill-wave"></i> Plan de Pagos
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Responsive Cards for Mobile -->
            <div class="responsive-table">
                <?php 
                $result->data_seek(0); // Reset result pointer
                $i = 0;
                while ($row = $result->fetch_assoc()): 
                    $status = $statuses[$i % count($statuses)];
                    $i++;
                ?>
                <div class="student-card">
                    <div class="student-header">
                        <div class="student-matricula"><?= htmlspecialchars($row['matricula']) ?></div>
                        <span class="status-badge <?= $status ?>"><?= $status == 'active' ? 'Activo' : 'Inactivo' ?></span>
                    </div>
                    <div class="student-name"><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno']) ?></div>
                    <div class="student-actions">
                        <a href="plan_pagos_alumno.php?matricula=<?= urlencode($row['matricula']) ?>" class="btn btn-primary">
                            <i class="fas fa-money-bill-wave"></i> Plan de Pagos
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="pagination">
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button><i class="fas fa-chevron-right"></i></button>
        </div>
        
        <footer>
            <p>Sistema de Control Escolar © <?= date('Y') ?> | Todos los derechos reservados</p>
        </footer>
    </div>

    <script>
        // Funcionalidad de búsqueda
        document.getElementById('searchButton').addEventListener('click', filterStudents);
        document.getElementById('searchInput').addEventListener('input', filterStudents);
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') filterStudents();
        });

        function filterStudents() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const desktopRows = document.querySelectorAll('.desktop-table tbody tr');
            const mobileCards = document.querySelectorAll('.responsive-table .student-card');
            let found = false;
            
            // Filtrar tabla de escritorio
            desktopRows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                if (rowText.includes(searchTerm)) {
                    row.style.display = '';
                    row.classList.add('highlight');
                    setTimeout(() => row.classList.remove('highlight'), 1000);
                    found = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Filtrar tarjetas móviles
            mobileCards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                if (cardText.includes(searchTerm)) {
                    card.style.display = '';
                    card.classList.add('highlight');
                    setTimeout(() => card.classList.remove('highlight'), 1000);
                    found = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Mostrar mensaje si no se encuentran resultados
            if (!found && searchTerm) {
                alert('No se encontraron alumnos con ese criterio de búsqueda');
            }
        }
        
        // Animación para elementos al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.card, .controls, .error-message');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('fade-in');
                }, 150 * index);
            });
        });
        
        // Simular funcionalidad de filtros
        document.querySelector('.filter-btn').addEventListener('click', function() {
            alert('Funcionalidad de filtros: En desarrollo');
        });
        
        // Simular funcionalidad de ordenamiento
        document.querySelector('.sort-btn').addEventListener('click', function() {
            alert('Funcionalidad de ordenamiento: En desarrollo');
        });
    </script>
</body>
</html>