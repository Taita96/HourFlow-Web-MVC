import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('overtimeChart');

    if (!canvas) {
        return;
    }

    const month = canvas.dataset.month;

    fetch(`/records/chart-data?mes=${month}`)
        .then((response) => response.json())
        .then((data) => {
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: data.map((item) => item.fecha),
                    datasets: [
                        {
                            label: 'Planeado (h)',
                            data: data.map((item) => item.planeado_horas),
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        },
                        {
                            label: 'Trabajado (h)',
                            data: data.map((item) => item.trabajado_horas),
                            backgroundColor: 'rgba(34, 197, 94, 0.6)',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true },
                    },
                },
            });
        });
});