@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-lg p-6">
    <div class="bg-white p-4 rounded shadow border mb-4">
        <h2 class="h4 font-weight-bold text-gray-800 mb-2">
            Selamat Datang, {{ ucfirst($user->role) }}!
        </h2>
    </div>

    @if ($user->role === 'petugas')
        <div class="bg-white p-4 rounded shadow border">
            <div class="bg-light rounded-lg overflow-hidden">
                <div class="text-center bg-light py-3 font-weight-semibold text-gray-600">
                    Total Penjualan Hari Ini
                </div>
                <div class="py-4 text-center">
                    <p class="h2 font-weight-bold text-gray-800">{{ $totalPenjualan }}</p>
                    <p class="mt-2 text-muted">Jumlah total penjualan yang terjadi hari ini.</p>
                </div>
                <div class="text-center text-xs text-muted bg-light py-2">
                    Terakhir diperbarui: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}
                </div>
            </div>
        </div>

    @elseif ($user->role === 'admin')
        <div class="row g-4">
            <!-- Pie Chart -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 font-weight-semibold text-gray-800 mb-4">Persentase Penjualan Produk</h3>
                        <div class="d-flex justify-content-center">
                            <div class="w-32 h-32 sm:w-40 sm:h-40"> <!-- Ukuran canvas lebih besar -->
                                <canvas id="overallPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 font-weight-semibold text-gray-800 mb-4">Grafik Penjualan Produk</h3>
                        <div class="d-flex justify-content-center">
                            <div class="w-100 sm:w-75 mx-auto"> <!-- Ukuran canvas lebih besar -->
                                <canvas id="salesChart" height="300"></canvas> <!-- Atur tinggi bar chart -->
                            </div>
                        </div>
                        @if(empty($chartData))
                            <p class="text-muted mt-2">Belum ada data untuk ditampilkan dalam grafik.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pie Chart
        const produkLabels = @json(array_keys($totalPerProduk));
        const produkTotals = @json(array_values($totalPerProduk));
        const pieColors = [
            '#f87171', '#60a5fa', '#fbbf24', '#34d399', '#c084fc',
            '#f97316', '#ec4899', '#22d3ee', '#818cf8', '#fde68a',
            '#86efac', '#fca5a5'
        ];

        const ctxPie = document.getElementById('overallPieChart')?.getContext('2d');
        if (ctxPie && produkLabels.length > 0) {
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: produkLabels,
                    datasets: [{
                        data: produkTotals,
                        backgroundColor: pieColors,
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
        }

        // Bar Chart
        const rawData = @json($chartData);
        if (rawData.length > 0) {
            const labels = rawData.map(item => item.tanggal);
            const totalPenjualan = rawData.map(item => {
                const sum = Object.entries(item)
                    .filter(([key]) => key !== 'tanggal')
                    .reduce((acc, [_, val]) => acc + (val ?? 0), 0);
                return sum;
            });

            const ctx = document.getElementById('salesChart')?.getContext('2d');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Penjualan',
                            data: totalPenjualan,
                            backgroundColor: 'rgba(59, 130, 246, 0.3)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Agar chart tetap responsif
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { mode: 'index', intersect: false },
                        },
                        scales: {
                            x: {
                                ticks: { maxRotation: 60, minRotation: 45 }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endpush
