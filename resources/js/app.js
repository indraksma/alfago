import Chart from 'chart.js/auto';

let charts = [];
window.renderDashboardCharts = (labels, sales, statuses) => {
    charts.forEach(chart => chart.destroy());
    charts = [];
    const salesCanvas = document.getElementById('salesChart');
    const statusCanvas = document.getElementById('statusChart');
    if (salesCanvas) charts.push(new Chart(salesCanvas, {
        type: 'line',
        data: { labels, datasets: [{ label: 'Omzet', data: sales, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,.1)', fill: true, tension: .35 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    }));
    if (statusCanvas) charts.push(new Chart(statusCanvas, {
        type: 'doughnut',
        data: { labels: Object.keys(statuses), datasets: [{ data: Object.values(statuses), backgroundColor: ['#facc15','#3b82f6','#10b981'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    }));
};
