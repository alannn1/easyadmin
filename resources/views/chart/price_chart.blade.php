<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chart Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div style="width: 345px; margin: 0 auto;">
        <canvas id="doughnutChart"></canvas>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var label_price = <?php echo json_encode($priceChart->pluck("product_name")); ?>;
            
            var value_price = <?php echo json_encode($priceChart->pluck("total_price")); ?>;

            const ctx = document.getElementById('doughnutChart').getContext('2d');

            const data = {
                labels: label_price,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: value_price,
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
                            text: 'Penjualan Produk'
                        }
                    }
                }
            };

            new Chart(ctx, config);
        })
    </script>
</body>
</html>
