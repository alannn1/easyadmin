<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chart Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div style="width: 345px; margin: 0 auto;">
        <canvas id="quantityChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var labels = <?php echo json_encode($quantityChart->pluck("product_name")); ?>;
            var values = <?php echo json_encode($quantityChart->pluck("quantity")); ?>;
            const ctx = document.getElementById('quantityChart').getContext('2d');

            const data = {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: values,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Penjualan Produk Berdasarkan Quantity'
                        }
                    }
                }
            };

            new Chart(ctx, config);
        })
    </script>
</body>
</html>
