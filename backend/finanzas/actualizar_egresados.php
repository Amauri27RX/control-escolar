<?php
require '../conexion.php';

function obtenerCicloActual() {
    $mes = date('n');
    $año = date('Y');
    $cuatri = ($mes >= 1 && $mes <= 4) ? 1 : (($mes >= 5 && $mes <= 8) ? 2 : 3);
    return "$año-$cuatri";
}

$cantidad = 0;
$actualizados = [];
$fecha_actualizacion = date('Y-m-d');

if (isset($_POST['actualizar'])) {
    $ciclo_actual = obtenerCicloActual();
    $estatus = 'Egresado';

    
    $query = "
        SELECT ia.matricula, ia.ciclo_fin, a.dgp, a.tiene_maestria_previa,
            p.nivel_educativo, p.total_materias, 
            p.materias_doctorado_licenciatura, p.materias_doctorado_maestria
        FROM inscripcion_alumno ia
        JOIN alumno a ON ia.matricula = a.matricula
        JOIN programa p ON a.dgp = p.dgp
        WHERE ia.estatus_alumno = 'Activo'
        AND ia.ciclo_fin <= '$ciclo_actual'
        AND ia.ciclo_finalizado IS NULL
    ";


    $res = $conn->query($query);

    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $matricula = $row['matricula'];
            $nivel = strtolower($row['nivel_educativo']);
            $materias_requeridas = 0;

            if ($nivel === 'doctorado') {
                $materias_requeridas = $row['tiene_maestria_previa'] ? 
                    $row['materias_doctorado_maestria'] : 
                    $row['materias_doctorado_licenciatura'];
            } else {
                $materias_requeridas = $row['total_materias'];
            }

            $q_aprobadas = "
                SELECT COUNT(*) AS aprobadas 
                FROM materias_alumno 
                WHERE matricula = '$matricula' AND aprobada = 1
            ";
            $res_ap = $conn->query($q_aprobadas);
            $aprobadas = $res_ap ? (int) $res_ap->fetch_assoc()['aprobadas'] : 0;

            // ❗Aquí es donde ahora se valida bien:
            if ($aprobadas >= $materias_requeridas) {
                $update = "
                    UPDATE inscripcion_alumno
                    SET estatus_alumno = 'Egresado',
                        ciclo_finalizado = CURDATE()
                    WHERE matricula = '$matricula'
                ";
                if ($conn->query($update)) {
                    $actualizados[$row['ciclo_fin']][] = $matricula;
                    $cantidad++;
                }
            }
        }
    }
}

// Mostrar egresados existentes (ya egresados)
$egresados_por_lote = [];
$q_egresados = "
    SELECT ia.matricula, a.nombre, a.apellido_paterno, a.apellido_materno, 
           a.fecha_inicio AS fecha_inicio_curso, 
           ia.ciclo_fin, DATE_FORMAT(ia.ciclo_finalizado, '%Y-%m-%d') as ciclo_finalizado
    FROM inscripcion_alumno ia
    JOIN alumno a ON ia.matricula = a.matricula
    WHERE ia.estatus_alumno = 'Egresado'
    ORDER BY ia.ciclo_fin DESC, ia.matricula ASC
";
$res_lotes = $conn->query($q_egresados);

if ($res_lotes && $res_lotes->num_rows > 0) {
    while ($row = $res_lotes->fetch_assoc()) {
        $lote = $row['ciclo_fin'];
        $egresados_por_lote[$lote][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Egresados</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #f5f7ff, #eef2ff);
            padding: 40px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
        }
        h2 {
            color: #4361ee;
            margin-bottom: 20px;
        }
        button {
            background-color: #4361ee;
            border: none;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: #3a56d4;
        }
        .alert {
            background-color: #e6fff4;
            border-left: 4px solid #06d6a0;
            padding: 15px;
            margin-bottom: 30px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f5f5f5;
            color: #6c757d;
            text-align: left;
        }
        .lote-block {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Relación de Alumnos Egresados por Cuatrimestre</h2>

    <form method="POST">
        <button type="submit" name="actualizar">
            <i class="fas fa-sync-alt"></i> Actualizar Egresados
        </button>
    </form>

    <?php if (!empty($actualizados)): ?>
        <div class="alert"><?= $cantidad ?> alumno(s) fueron actualizados como egresados el <?= $fecha_actualizacion ?>.</div>
        <?php foreach ($actualizados as $ciclo => $alumnos): ?>
            <div class="lote-block">
                <h3>Lote: <?= $ciclo ?> (<?= count($alumnos) ?> alumno(s))</h3>
                <table>
                    <thead><tr><th>Matrícula</th></tr></thead>
                    <tbody>
                        <?php foreach ($alumnos as $mat): ?>
                            <tr><td><?= $mat ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php foreach ($egresados_por_lote as $ciclo => $egresados): ?>
        <div class="lote-block">
            <h3>Lote: <?= $ciclo ?> (<?= count($egresados) ?> alumno(s))</h3>
            <table>
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Nombre</th>
                        <th>Inicio del Curso</th>
                        <th>Fecha de Egreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($egresados as $e): ?>
                        <tr>
                            <td><?= $e['matricula'] ?></td>
                            <td><?= $e['nombre'] . ' ' . $e['apellido_paterno'] . ' ' . $e['apellido_materno'] ?></td>
                            <td><?= $e['fecha_inicio_curso'] ?></td>
                            <td><?= $e['ciclo_finalizado'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
