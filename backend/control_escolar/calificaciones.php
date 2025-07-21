<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Académica</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="calificaciones.css"> 
</head>
<body class="bg-gray-100">

    <div id="app" class="min-h-screen">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-bold text-gray-900">Sistema de Gestión Académica</h1>
            </div>
        </header>
        
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <div id="student-list-container" class="lg:col-span-1">
                    <div class="space-y-6">
                        <h2 class="text-2xl font-bold mb-4">Gestión de Estudiantes</h2>
                        
                        <div class="flex flex-col gap-4 mb-6">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por Alumno</label>
                                <input type="text" id="search" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Nombre o matrícula...">
                            </div>
                            <div>
                                <label for="filtroInstitucion" class="block text-sm font-medium text-gray-700 mb-1">Institución</label>
                                <select id="filtroInstitucion" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Todas las Instituciones</option>
                                    </select>
                            </div>
                             <div>
                                <label for="filtroNivel" class="block text-sm font-medium text-gray-700 mb-1">Nivel Educativo</label>
                                <select id="filtroNivel" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Todos los Niveles</option>
                                     </select>
                            </div>
                            <div>
                                <label for="filtroPrograma" class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                                <select id="filtroPrograma" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Todos los Programas</option>
                                    </select>
                            </div>
                            <div>
                                <label for="filtroStatus" class="block text-sm font-medium text-gray-700 mb-1">Estatus del Alumno</label>
                                <select id="filtroStatus" class="w-full p-2 border border-gray-300 rounded-md">
                                    <option value="">Todos los Estatus</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Egresado">Egresado</option>
                                    <option value="Baja">Baja</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th></tr>
                                </thead>
                                <tbody id="student-list-body" class="bg-white divide-y divide-gray-200">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div id="student-detail-container" class="lg:col-span-3"></div>

            </div>
        </main>
        
        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-center text-sm text-gray-500">
                    Sistema de Gestión Académica
                </p>
            </div>
        </footer>
    </div>

    <script src="../../frontend/js/control_escolar/calificaciones.js"></script>
</body>
</html>