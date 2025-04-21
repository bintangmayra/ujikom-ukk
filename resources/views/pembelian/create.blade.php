{{-- resources/views/pembelian/select-products.blade.php --}}
@extends('layouts.app')
@section('title', 'Pilih Produk')

@section('content')
<div class="card p-4 rounded-4">
    <h5 class="fw-bold mb-4">Pilih Produk</h5>
    <form action="{{ route(auth()->user()->role . '.pembelian.checkout') }}" method="POST">
        @csrf
        @foreach($products as $product)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="products[]" value="{{ $product->id }}" id="product{{ $product->id }}">
                <label class="form-check-label" for="product{{ $product->id }}">
                    {{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})
                </label>
            </div>
        @endforeach
        <button type="submit" class="btn btn-primary mt-3">Selanjutnya</button>
    </form>
</div>
@endsection
