@extends('layouts.app')

@section('content')
<div class="container-lg p-6">
    <div class="bg-white p-4 rounded shadow border mb-4">
        <h2 class="h4 font-weight-bold text-gray-800 mb-2">
            Update Stok Produk
        </h2>
        <p class="text-muted">Halaman ini memungkinkan Anda untuk memperbarui stok produk.</p>
    </div>

    <!-- Produk Table -->
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.produk.update-stock', $produk->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control bg-light" value="{{ $produk->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">Stok Baru</label>
                    <input type="number" name="stock" id="stock" value="{{ $produk->stock }}" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
