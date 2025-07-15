document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.carousel-container .slide');
    let currentIndex = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
    }

    const nextBtn = document.querySelector('.carousel-next-a');
    const prevBtn = document.querySelector('.carousel-prev-a');

    if (nextBtn && prevBtn && slides.length > 0) {
        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        });

        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        });

        showSlide(currentIndex); // Mostrar el primer slide
    }

    const graficas = [
        {
            id: 'chartUnac',
            label: 'UNAC',
            programas: ['LEA', 'LECN', 'LEEF', 'MAF', 'MEDEF', 'MEEL', 'DDEA', 'DEEI', 'DEFAD'],
            alumnos: [20, 5, 30, 10, 2, 12, 5, 20, 11]
        },
        {
            id: 'chartIuac',
            label: 'IUAC',
            programas: ['MAH', 'MDP', 'DAPPP'],
            alumnos: [25, 2, 7]
        },
        {
            id: 'chartIuacMx',
            label: 'IUAC de México',
            programas: ['LCPF', 'LPOE', 'MAGSE', 'MADIE', 'MDEEMS', 'DGPH', 'DFGPP'],
            alumnos: [12, 5, 28, 8, 12, 24, 19]
        },
        {
            id: 'chartUcceg',
            label: 'UCCEG',
            programas: ['LD', 'LAD', 'ME', 'MAGRO', 'DPG'],
            alumnos: [12, 15, 5, 8, 1]
        }
    ];

    graficas.forEach((grafica) => {
        const ctx = document.getElementById(grafica.id).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: grafica.programas,
                datasets: [{
                    label: `Alumnos - ${grafica.label}`,
                    data: grafica.alumnos,
                    backgroundColor: 'rgba(159, 245, 137, 0.82)',
                    borderColor: 'rgba(101, 189, 79, 0.82)',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Arial',
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333',
                            padding: 20
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
});
