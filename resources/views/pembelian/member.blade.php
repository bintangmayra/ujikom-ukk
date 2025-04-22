@extends('layouts.app')
@section('title', 'Checkout Pembelian')

@section('content')
<div class="card p-4 rounded-4">
    <h5 class="fw-bold mb-4">Checkout Pembelian</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
    // Hitung total harga dengan mempertimbangkan jumlah
    $total_price = $products->sum(function($product) {
        return $product->price * $product->jumlah;
    });

    // Ambil data poin yang tersedia dari memberData
    $availablePoints = $memberData ? $memberData->poin : 0;
    @endphp

    <form action="{{ route(auth()->user()->role . '.pembelian.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Kiri --}}
            <div class="col-md-6">
                <h6>Produk yang Dipilih</h6>
                <ul class="list-group mb-3">
                    @foreach($products as $product)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $product->name }} X {{ $product->jumlah }}</span>
                        <span>Rp {{ number_format($product->price * $product->jumlah, 0, ',', '.') }}</span>
                    </li>
                    <input type="hidden" name="products[]" value="{{ $product->id }}">
                    @endforeach

                    <li class="list-group-item d-flex justify-content-between fw-bold bg-light">
                        <span>Total</span>
                        <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                        <input type="hidden" name="total_price" value="{{ $total_price }}">
                    </li>
                </ul>
            </div>

            {{-- Kanan --}}
            <div class="col-md-6">
                <input type="hidden" name="status_member" value="member">
                <div id="memberDetail">
                    <div class="mb-3">
                        <label for="member_phone" class="form-label">No Telepon</label>
                        <input type="text" id="member_phone" name="member_phone" class="form-control" placeholder="No Telepon" value="{{$memberPhone}}">
                    </div>
                    <div class="mb-3">
                        <label for="member_name" class="form-label">Nama Member</label>
                        <input type="text" id="member_name" name="member_name" class="form-control" placeholder="Nama Member" value="{{ $memberData ? $memberData->name : '' }}">
                        <input type="hidden" name="member_id" id="member_id" value="{{ $memberData ? $memberData->id : '' }}">
                    </div>

                    @if($memberData && $memberData->poin)
                    <div id="usePointsSection" class="mb-3">
                        <label for="use_points" class="form-label">Apakah Anda Ingin Menggunakan Poin?</label>

                        {{-- Tampilkan checkbox untuk memilih apakah member ingin menggunakan poin atau tidak --}}
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="use_points" value="1" id="use_points" {{ $availablePoints > 0 ? '' : 'disabled' }}>
                            <label class="form-check-label" for="use_points">Gunakan Poin (Anda memiliki {{ $availablePoints }} poin)</label>
                        </div>
                    </div>

                    @else
                    <div id="usePointsSection" class="alert alert-info">
                        Anda belum dapat menggunakan poin karena ini adalah pembelian pertama.
                    </div>
                    @endif

                    <input type="hidden" name="total_payment" id="total_payment" value="{{ $total_payment }}">
                </div>

                <input type="hidden" name="member_id" id="member_id">

                <button type="submit" class="btn btn-success w-100 mt-5">Selesaikan Pembelian</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
        const totalInput = document.getElementById('total_payment');

        // Format Rupiah otomatis
        totalInput.addEventListener('input', function () {
            const raw = this.value.replace(/[^\d]/g, ''); // Menghapus karakter selain angka
            this.value = formatRupiah(raw);
        });

        function formatRupiah(angka, prefix = 'Rp. ') {
            let number_string = angka.toString().replace(/[^,\d]/g, ''), split = number_string.split(','), sisa = split[0].length % 3, rupiah = split[0].substr(0, sisa), ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return prefix + rupiah;
        }
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        const totalInput = document.getElementById('total_payment');
        if (totalInput) {
            const rawTotal = totalInput.value.replace(/[^\d]/g, ''); // Menghapus semua karakter non-numerik
            totalInput.value = rawTotal; // Perbarui nilai input dengan angka
            console.log("Total Pembayaran:", rawTotal); // Cek nilai total pembayaran yang dikirim
        }

        // Cek semua data yang dikirimkan saat form submit
        const formData = new FormData(this); // Ambil semua data dari form
        formData.forEach((value, key) => {
            console.log(`${key}: ${value}`); // Tampilkan nama dan nilai dari setiap field
        });
    });
</script>
@endpush
