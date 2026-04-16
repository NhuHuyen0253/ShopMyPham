document.addEventListener('DOMContentLoaded', function () {
    const chartDataEl = document.getElementById('revenueChartData');
    if (!chartDataEl || typeof Chart === 'undefined') return;

    const dailyLabels = JSON.parse(chartDataEl.dataset.dailyLabels || '[]');
    const dailyValues = JSON.parse(chartDataEl.dataset.dailyValues || '[]');
    const monthlyLabels = JSON.parse(chartDataEl.dataset.monthlyLabels || '[]');
    const monthlyValues = JSON.parse(chartDataEl.dataset.monthlyValues || '[]');

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return context.dataset.label + ': ' + Number(context.raw).toLocaleString('vi-VN') + ' đ';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        new Chart(revenueCanvas, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Doanh thu theo ngày',
                    data: dailyValues,
                    borderWidth: 1,
                    borderRadius: 8,
                    maxBarThickness: 42
                }]
            },
            options: commonOptions
        });
    }

    const monthlyCanvas = document.getElementById('monthlyRevenueChart');
    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Doanh thu theo tháng',
                    data: monthlyValues,
                    borderWidth: 1,
                    borderRadius: 10,
                    maxBarThickness: 50
                }]
            },
            options: commonOptions
        });
    }
});