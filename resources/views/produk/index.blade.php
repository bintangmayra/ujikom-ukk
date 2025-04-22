@extends('layouts.app')

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
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>
        <h4 class="fw-bold">Produk</h4>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-4">
    <!-- "Tambah Produk" button placed to the left -->
    <div>
        @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.produk.create') }}" class="btn btn-primary shadow-sm me-2">
                <i class="fas fa-plus me-2"></i> Tambah Produk
            </a>
            <a href="{{ route('admin.produk.export') }}" class="btn btn-success shadow-sm">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </a>
        @endif
    </div>


    <!-- Search Form placed to the right -->
    <form action="{{ url()->current() }}" method="GET" class="d-flex mb-4" style="max-width: 400px;">
        {{-- <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Cari produk..."> --}}
        {{-- <button type="submit" class="btn btn-primary">
            <i class="fas fa-search me-1"></i> Cari
        </button> --}}
    </form>
</div>

<!-- Produk Table -->
<div class="card shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        @if(Auth::user()->role === 'admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $index => $produk)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                @if ($produk->image)
                                    <img src="{{ asset($produk->image) }}" width="60" class="rounded shadow-sm" alt="gambar">
                                @else
                                    <span class="text-muted">Tidak Ada</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $produk->name }}</td>
                            <td>Rp. {{ number_format($produk->price, 0, ',', '.') }}</td>
                            <td>{{ $produk->stock }}</td>
                            @if(Auth::user()->role === 'admin')
                            <td>
                                <a href="{{ route('admin.produk.edit', $produk->id) }}" class="btn btn-warning btn-sm me-1">Edit</a>

                                <!-- Tombol Ubah Stok -->
                                <button type="button" class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editStockModal{{ $produk->id }}">
                                    Update Stock
                                </button>

                                <!-- Hapus -->
                                <form action="{{ route('admin.produk.destroy', $produk->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
                                </form>
                            </td>
                            @endif
                        </tr>

                        <!-- Modal Edit Stock -->
                        <div class="modal fade" id="editStockModal{{ $produk->id }}" tabindex="-1" aria-labelledby="editStockModalLabel{{ $produk->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.produk.update-stock', $produk->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editStockModalLabel{{ $produk->id }}">Update Stok - {{ $produk->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Produk</label>
                                                <input type="text" class="form-control bg-light" value="{{ $produk->name }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="stock{{ $produk->id }}" class="form-label">Stok Baru</label>
                                                <input type="number" name="stock" id="stock{{ $produk->id }}" value="{{ $produk->stock }}" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tidak ada produk ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
