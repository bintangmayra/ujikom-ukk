@extends('layouts.app')

@section('title', 'Dashboard')

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(empty($chartData))
                <p class="text-muted mt-2">Belum ada data untuk ditampilkan dalam grafik.</p>
            @else
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Grafik Penjualan Produk</h5>
                        <canvas id="overallPieChart"></canvas>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pastikan $totalPerProduk adalah koleksi
        const produkLabels = @json($totalPerProduk->keys());  // Menggunakan keys() untuk koleksi
        const produkTotals = @json($totalPerProduk->values());  // Menggunakan values() untuk koleksi

        const totalProduk = produkTotals.reduce((a, b) => a + b, 0);

        const ctxPie = document.getElementById('overallPieChart').getContext('2d');

        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: produkLabels.map((label, index) => {
                    const percent = ((produkTotals[index] / totalProduk) * 100).toFixed(1);
                    return `${label} (${percent}%)`;
                }),
                datasets: [{
                    data: produkTotals,
                    backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#F9FF33'],
                }]
            }
        });
    });
</script>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pie Chart: Persentase Penjualan Produk

        const produkLabels = @json($totalPerProduk->keys());  // Menggunakan keys() untuk koleksi
        const produkTotals = @json($totalPerProduk->values());  // Menggunakan values() untuk koleksi

        const totalProduk = produkTotals.reduce((a, b) => a + b, 0);

        const ctxPie = document.getElementById('overallPieChart').getContext('2d');

        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: produkLabels.map((label, index) => {
                    const percent = ((produkTotals[index] / totalProduk) * 100).toFixed(1);
                    return `${label} (${percent}%)`;
                }),
                datasets: [{
                    data: produkTotals,
                    backgroundColor: [
                        '#f87171', '#60a5fa', '#fbbf24', '#34d399', '#c084fc',
                        '#f97316', '#ec4899', '#22d3ee', '#818cf8', '#fde68a',
                        '#86efac', '#fca5a5'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(2) + '%';
                                return `${label}: ${value} (${percentage})`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

