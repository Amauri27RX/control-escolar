<?php
require '../conexion.php';

function calcularProgreso($materias_aprobadas, $total_materias) {
    if ($total_materias <= 0) return 0;
    return min(100, round(($materias_aprobadas / $total_materias) * 100));
}

$institucion_filtro = isset($_GET['institucion']) ? $_GET['institucion'] : '';
$nivel_filtro = isset($_GET['nivel']) ? $_GET['nivel'] : '';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$estatus_filtro = isset($_GET['estatus']) ? $_GET['estatus'] : '';

$sql = "
    SELECT a.matricula, a.nombre, a.apellido_paterno, a.apellido_materno,
           a.tiene_maestria_previa, a.fecha_inicio, a.dgp,
           p.nombre_programa, p.nivel_educativo, p.id_institucion,
           i.nombre AS nombre_institucion,
           ia.estatus_alumno,
           p.total_materias, p.materias_doctorado_maestria,
           p.materias_doctorado_licenciatura,
           (
             SELECT COUNT(*) FROM materias_alumno ma
             WHERE ma.matricula = a.matricula AND ma.aprobada = 1
           ) AS materias_aprobadas
    FROM alumno a
    JOIN inscripcion_alumno ia ON a.matricula = ia.matricula
    JOIN programa p ON a.dgp = p.dgp
    JOIN institucion i ON p.id_institucion = i.id_institucion
    WHERE 1=1
";

if (!empty($institucion_filtro)) {
    $institucion_filtro = $conn->real_escape_string($institucion_filtro);
    $sql .= " AND p.id_institucion = '$institucion_filtro'";
}

if (!empty($nivel_filtro)) {
    $nivel_filtro = $conn->real_escape_string($nivel_filtro);
    $sql .= " AND p.nivel_educativo = '$nivel_filtro'";
}

if (!empty($estatus_filtro)) {
    $estatus_filtro = $conn->real_escape_string($estatus_filtro);
    $sql .= " AND ia.estatus_alumno = '$estatus_filtro'";
}

if (!empty($busqueda)) {
    $busqueda_sql = $conn->real_escape_string($busqueda);
    $sql .= " AND (a.matricula LIKE '%$busqueda_sql%' OR a.nombre LIKE '%$busqueda_sql%' OR a.apellido_paterno LIKE '%$busqueda_sql%' OR a.apellido_materno LIKE '%$busqueda_sql%')";
}

$sql .= " ORDER BY p.nombre_programa, a.apellido_paterno, a.apellido_materno";

$result = $conn->query($sql);
$alumnos_por_programa = [];

$instituciones = $conn->query("SELECT id_institucion, nombre FROM institucion ORDER BY nombre ASC");
$niveles = $conn->query("SELECT DISTINCT nivel_educativo FROM programa ORDER BY nivel_educativo ASC");
$estatuses = $conn->query("SELECT DISTINCT estatus_alumno FROM inscripcion_alumno ORDER BY estatus_alumno ASC");

while ($row = $result->fetch_assoc()) {
    $programa = $row['nombre_programa'];

    $total_materias = 0;
    if (strtolower($row['nivel_educativo']) === 'doctorado') {
        $total_materias = !empty($row['tiene_maestria_previa'])
            ? $row['materias_doctorado_maestria']
            : $row['materias_doctorado_licenciatura'];
    } else {
        $total_materias = $row['total_materias'];
    }

    $progreso = calcularProgreso($row['materias_aprobadas'], $total_materias);
    $estatus = $row['estatus_alumno'];

    if ($progreso >= 100 && $estatus != 'Egresado') {
        $matricula = $conn->real_escape_string($row['matricula']);
        $conn->query("UPDATE inscripcion_alumno SET estatus_alumno = 'Egresado', ciclo_finalizado = CURDATE() WHERE matricula = '$matricula' AND estatus_alumno != 'Egresado'");
        $estatus = 'Egresado';
    }

    $row['progreso'] = $progreso;
    $row['total_materias'] = $total_materias;
    $row['estatus'] = $estatus;

    $alumnos_por_programa[$programa][] = $row;
}
?>.

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Progreso Académico de Alumnos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body {
            background-color: #f8f9fb;
            font-family: 'Inter', sans-serif;
        }
        .header-section {
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            background-color: #2b3e6c;
            padding: 2rem;
            color: white;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .filter-bar {
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0 6px rgba(0,0,0,0.05);
        }
        .card-header {
            background-color: #1f3c88;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .table thead {
            background-color: #eef1f8;
        }
        .progress {
            height: 22px;
            border-radius: 0.25rem;
        }
        .progress-bar {
            font-size: 13px;
            font-weight: 600;
        }
        .btn-export, .btn-search {
            min-width: 100px;
            width: 100%;
            max-width: 180px;
            width: 45px;
            height: 38px;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="header-section">
        <h2>Seguimiento del Progreso Académico</h2>
    </div>
    <form method="GET" class="filter-bar row g-3 align-items-center">
        <div class="col-md-3">
            <input type="text" name="busqueda" class="form-control" placeholder="Buscar por matrícula o nombre" value="<?= htmlspecialchars($busqueda) ?>">
        </div>
        <div class="col-md-2">
            <select name="institucion" class="form-select">
                <option value="">Todas las instituciones</option>
                <?php mysqli_data_seek($instituciones, 0); while ($inst = $instituciones->fetch_assoc()): ?>
                    <option value="<?= $inst['id_institucion'] ?>" <?= $inst['id_institucion'] == $institucion_filtro ? 'selected' : '' ?>><?= htmlspecialchars($inst['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="nivel" class="form-select">
                <option value="">Todos los niveles</option>
                <?php mysqli_data_seek($niveles, 0); while ($nivel = $niveles->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($nivel['nivel_educativo']) ?>" <?= $nivel['nivel_educativo'] == $nivel_filtro ? 'selected' : '' ?>><?= htmlspecialchars($nivel['nivel_educativo']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="estatus" class="form-select">
                <option value="">Todos los estatus</option>
                <?php mysqli_data_seek($estatuses, 0); while ($e = $estatuses->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($e['estatus_alumno']) ?>" <?= $e['estatus_alumno'] == $estatus_filtro ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['estatus_alumno']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary btn-search"><i class="fas fa-search"></i></button>
        </div>
        <div class="col-md-2 d-grid">
            <button type="button" class="btn btn-success btn-export" onclick="exportarExcel()"><i class="fas fa-file-excel"></i></button>
        </div>
    </form>

    <?php foreach ($alumnos_por_programa as $programa => $alumnos): ?>
    <div class="card mb-4">
        <div class="card-header"><?= htmlspecialchars($programa) ?></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered exportable">
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Materias Aprobadas</th>
                            <th>Progreso</th>
                            <th>Estatus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['matricula']) ?></td>
                            <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido_paterno'] . ' ' . $row['apellido_materno']) ?></td>
                            <td><?= htmlspecialchars($row['fecha_inicio']) ?></td>
                            <td><?= $row['materias_aprobadas'] ?> / <?= $row['total_materias'] ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar <?= $row['progreso'] >= 100 ? 'bg-success' : 'bg-primary' ?>" style="width: <?= $row['progreso'] ?>%">
                                        <?= $row['progreso'] ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?= $row['estatus'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<script>
function exportarExcel() {
    const tables = document.querySelectorAll('.exportable');
    const wb = XLSX.utils.book_new();

    tables.forEach((table, index) => {
        // Buscar el título del programa desde el encabezado anterior
        const card = table.closest('.card');
        const header = card.querySelector('.card-header');
        const nombrePrograma = header ? header.textContent.trim() : 'Programa_' + (index + 1);

        const ws = XLSX.utils.table_to_sheet(table);
        XLSX.utils.book_append_sheet(wb, ws, nombrePrograma.substring(0, 31)); // Excel permite máx. 31 caracteres
    });

    XLSX.writeFile(wb, 'Progreso_Alumnos_Filtrado.xlsx');
}
</script>
</body>
</html>
