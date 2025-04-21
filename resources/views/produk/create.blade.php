@extends('layouts.app')

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">🏠</a></li>
            <li class="breadcrumb-item active" aria-current="page">Produk</li>
        </ol>
    </nav>

    <h3 class="mb-4 fw-bold">Produk</h3>

    <div class="card shadow-sm p-4 rounded-4 border-0">
        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama produk" required>
                </div>

                <div class="col-md-6">
                    <label for="image" class="form-label">Gambar Produk <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="image" name="image" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" placeholder="Masukkan harga produk" required>
                </div>

                <div class="col-md-6">
                    <label for="stock" class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock" name="stock" placeholder="Masukkan stok produk" required>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
