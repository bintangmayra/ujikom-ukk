@extends('layouts.app')

@section('content')
<h2 class="fw-bold mb-4">Edit Produk</h2>

<form action="{{ route('admin.produk.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Stok</label>
        <input type="number" name="stock" value="{{ $product->stock }}" class="form-control" required readonly style="background-color: #f0f0f0;">
    </div>

    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="price" value="{{ $product->price }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Gambar (opsional)</label>
        <input type="file" name="image" class="form-control">
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" width="100" class="mt-2 rounded shadow-sm">
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
