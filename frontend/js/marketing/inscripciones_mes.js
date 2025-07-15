document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartInscripciones').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar', // Puede cambiar a 'line', 'pie', etc.
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Inscripciones',
                data: [12, 19, 8, 15, 22, 30, 25, 18, 20, 10, 5, 7], // <-- <?php echo json_encode($datos); ?>,
                backgroundColor: 'rgb(232, 165, 238)',
                borderColor: 'rgb(197, 100, 221)',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom', // ← Leyenda abajo de la gráfica
                    labels: {
                        font: {
                            family: 'Arial', // Fuente
                            size: 14,         // Tamaño
                            weight: 'bold',    // Grosor
                        },
                        color: '#444',        // Color del texto (ej. gris oscuro)
                        padding: 10
                    }       
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#222' // ← Color de etiquetas del eje X
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#222' // ← Color de etiquetas del eje Y
                    }
                }
            }
        }
    });
});
