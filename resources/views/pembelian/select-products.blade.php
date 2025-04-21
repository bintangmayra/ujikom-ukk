@extends('layouts.app')
@section('title', 'Pilih Produk')

@section('content')
<div class="card p-4 rounded-4">
    <h5 class="fw-bold mb-4">Pilih Produk</h5>

    <form action="{{ route(auth()->user()->role . '.pembelian.checkout') }}" method="POST" onsubmit="return filterProducts()">
        @csrf
        <div class="row">
            @foreach($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm rounded-4 overflow-hidden">
                    <img src="{{ asset( $product->image) }}" alt="{{ $product->name }}"
                         class="w-100" style="height: 180px; object-fit: cover;">

                    <div class="p-3 text-center">
                        <div class="form-check d-flex justify-content-center mb-2">
                            <label class="form-check-label fw-semibold text-capitalize" for="product{{ $product->id }}">
                                {{ $product->name }}
                            </label>
                        </div>

                        <p class="mb-0">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p class="text-muted">Stok: <span id="stock-{{ $product->id }}">{{ $product->stock }}</span></p>

                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="kurangiJumlah({{ $product->id }}, {{ $product->price }})">-</button>
                                    <input type="number" class="form-control text-center" id="jumlah-{{ $product->id }}"
                                    name="products[{{ $product->id }}][jumlah]" value="0" min="0" max="{{ $product->stock }}"
                                    oninput="ubahJumlah({{ $product->id }}, {{ $product->stock }}, {{ $product->price }})">

                            <button type="button" class="btn btn-outline-secondary"
                                    onclick="tambahJumlah({{ $product->id }}, {{ $product->stock }}, {{ $product->price }})">+</button>
                        </div>
                        <p>Subtotal: <span id="subtotal-{{ $product->id }}" data-harga="{{ $product->price }}">Rp 0</span></p>

                        {{-- Menambahkan input tersembunyi untuk data produk --}}
                        <input type="hidden" name="products[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                        <input type="hidden" name="products[{{ $product->id }}][product_name]" value="{{ $product->name }}">
                        <input type="hidden" name="products[{{ $product->id }}][product_stock]" value="{{ $product->stock }}">
                        <input type="hidden" name="products[{{ $product->id }}][product_price]" value="{{ $product->price }}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-4" id="pesanBtn" disabled>Selanjutnya</button>
        </div>
    </form>
</div>


<script>
   function kurangiJumlah(id, harga) {
    let input = document.querySelector(`input[name="products[${id}][jumlah]"]`);
    let subtotal = document.getElementById('subtotal-' + id);
    let stockElement = document.getElementById('stock-' + id);
    let jumlah = parseInt(input.value);
    let stock = parseInt(stockElement.innerText);

    if (jumlah > 0) {
        input.value = jumlah - 1;
        subtotal.innerText = formatRupiah((jumlah - 1) * harga);
        stockElement.innerText = stock + 1;
    }

    updateButtonState();
}

function tambahJumlah(id, maxStock, harga) {
    let input = document.querySelector(`input[name="products[${id}][jumlah]"]`);
    let subtotal = document.getElementById('subtotal-' + id);
    let stockElement = document.getElementById('stock-' + id);
    let jumlah = parseInt(input.value);
    let stock = parseInt(stockElement.innerText);

    if (jumlah < maxStock) {
        input.value = jumlah + 1;
        subtotal.innerText = formatRupiah((jumlah + 1) * harga);
        stockElement.innerText = stock - 1;
    }

    updateButtonState();
}

function ubahJumlah(id, maxStock, harga) {
    let input = document.querySelector(`input[name="products[${id}][jumlah]"]`);
    let subtotal = document.getElementById('subtotal-' + id);
    let stockElement = document.getElementById('stock-' + id);
    let jumlah = parseInt(input.value);

    if (isNaN(jumlah) || jumlah < 0) jumlah = 0;
    if (jumlah > maxStock) jumlah = maxStock;

    input.value = jumlah; // Memperbarui input
    subtotal.innerText = formatRupiah(jumlah * harga); // Memperbarui subtotal
    stockElement.innerText = maxStock - jumlah; // Memperbarui stok

    updateButtonState(); // Memperbarui status tombol
}


function updateButtonState() {
    let jumlahProduk = 0;
    document.querySelectorAll('input[name^="products["]').forEach(input => {
        if (parseInt(input.value) > 0) {
            jumlahProduk++;
        }
    });

    let btnPesan = document.getElementById('pesanBtn');
    btnPesan.disabled = jumlahProduk === 0;
}

function filterProducts() {
    let formValid = true;
    document.querySelectorAll('input[name^="products["]').forEach(input => {
        let id = input.name.match(/\[(\d+)\]/)[1];  // Extract product id from name
        if (parseInt(input.value) === 0) {
            // Disable hidden inputs for products with quantity 0
            document.querySelectorAll(`input[name="products[${id}][product_id]"]`).forEach(hiddenInput => {
                hiddenInput.disabled = true;
            });
        }
    });
    return formValid;
}


function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

</script>


@endsection
