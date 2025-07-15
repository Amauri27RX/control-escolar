<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Académica</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../frontend/css/control_escolar/calificaciones.css">
</head>
<body class="bg-gray-100">

    <div id="app" class="min-h-screen">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Sistema de Gestión Académica</h1>
                </div>
            </div>
        </header>
        
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                <div id="student-list-container" class="lg:col-span-1"></div>
                
                <div id="student-detail-container" class="lg:col-span-3"></div>

            </div>
        </main>
        
        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-center text-sm text-gray-500">
                    Sistema de Gestión Académica - Instituto Universitario de las Américas y el Caribe
                </p>
            </div>
        </footer>
    </div>

    <script src="../../frontend/js/control_escolar/calificaciones.js"></script>
</body>
</html>