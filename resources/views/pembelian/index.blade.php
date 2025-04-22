@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="/dashboard">
                        <i class="fas fa-home me-1"></i> <!-- Ikon rumah -->
                    </a>
                </li>

                <li class="breadcrumb-item active" aria-current="page">Penjualan</li>
            </ol>
        </nav>
        <h4 class="fw-bold">Penjualan</h4>
    </div>
</div>

<div class="card rounded-4 p-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="d-flex gap-2">
            {{-- Tombol Export --}}
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.pembelian.export') }}" class="btn btn-primary">
                    Export Penjualan (.xlsx)
                </a>
            @elseif(auth()->user()->role === 'petugas')
                <a href="{{ route('petugas.pembelian.export') }}" class="btn btn-primary">
                    Export Penjualan (.xlsx)
                </a>
            @endif

            {{-- Tombol Tambah Penjualan untuk Petugas --}}
            @if(auth()->user()->role === 'petugas')
                <a href="{{ route('petugas.pembelian.create') }}" class="btn btn-success">
                    Tambah Penjualan
                </a>
            @endif
        </div>

        {{-- Form Pencarian --}}
        <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari Nama Pelanggan...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="pembelianTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Penjualan</th>
                    <th>Total Harga</th>
                    <th>Dibuat Oleh</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $index => $purchase)
                    <tr>
                        <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1 }}</td>
                        <td>{{ optional($purchase->member)->name ?? 'NON-MEMBER' }}</td>
                        <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                        <td>Rp {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                        <td>{{ optional($purchase->user)->name ?? 'Petugas' }}</td>
                        <td class="text-center">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.pembelian.show', $purchase->id) }}" class="btn btn-warning btn-sm">Lihat</a>
                                <a href="{{ route('admin.pembelian.download', $purchase->id) }}" class="btn btn-primary btn-sm">Unduh Bukti</a>
                            @elseif(auth()->user()->role === 'petugas')
                                <a href="{{ route('petugas.pembelian.show', $purchase->id) }}" class="btn btn-warning btn-sm">Lihat</a>
                                <a href="{{ route('petugas.pembelian.download', $purchase->id) }}" class="btn btn-primary btn-sm">Unduh Bukti</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada transaksi penjualan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-end mt-3">
        {{ $purchases->appends(request()->input())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: Live search di client-side
    document.querySelector('input[name="search"]').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('#pembelianTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(keyword) ? '' : 'none';
        });
    });
</script>
@endpush
