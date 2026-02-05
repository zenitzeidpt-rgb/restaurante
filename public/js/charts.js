fetch('../estadisticas/datos.php')
.then(res => res.json())
.then(data => {

const ctx = document.getElementById('graficoVentas');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: data.fechas,
        datasets: [{
            label: 'Ventas Bs',
            data: data.totales,
        }]
    }
});
});
