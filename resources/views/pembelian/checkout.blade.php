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
                @endforeach

                <li class="list-group-item d-flex justify-content-between fw-bold bg-light">
                    <span>Sub total</span>
                    <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                    <input type="hidden" name="total_price" value="{{ $total_price }}">
                </li>
            </ul>
        </div>

        {{-- Kanan --}}
        <div class="col-md-6">
            {{-- Status Member --}}
            <label for="status_member" class="form-label">Status Member <span class="text-danger">*</span></label>
            <select name="status_member" id="status_member" class="form-select mb-3" required>
                <option value="non-member">Bukan Member</option>
                <option value="member">Member</option>
            </select>

            {{-- Member Phone Number --}}
            <div class="mb-3" id="memberDetail" style="display:none;">
                <label for="member_phone" class="form-label">Nomor Telepon</label>
                <input type="text" id="member_phone" name="member_phone" class="form-control"
                       value="{{ $user->member->no_phone ?? '' }}" placeholder="Masukkan nomor telepon" required>
            </div>


      
            {{-- Total Bayar --}}
            <label for="total_payment_display" class="form-label">Total Bayar</label>
            <input type="text" id="total_payment" name="total_payment" class="form-control" placeholder="Masukkan nominal pembayaran" required autocomplete="off">

            {{-- Submit --}}
            <button type="submit" class="btn btn-success w-100 mt-5">Selesaikan Pembelian</button>
        </div>
    </div>
</form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const memberSelect = document.getElementById('status_member');
    const memberDetail = document.getElementById('memberDetail');
    const memberPhoneInput = document.getElementById('member_phone');
    const totalInput = document.getElementById('total_payment');

    // Fungsi untuk menampilkan atau menyembunyikan detail member
    function toggleMemberFields(status) {
        if (status === 'member') {
            memberDetail.style.display = 'block'; // Show member details (phone)
            memberPhoneInput.removeAttribute('readonly');  // Make the phone number editable for members
        } else {
            memberDetail.style.display = 'none'; // Hide member details (phone)
        }
    }

    // Menangani perubahan pilihan status member
    memberSelect.addEventListener('change', function () {
        toggleMemberFields(this.value);
    });

    // Fungsi untuk format Rupiah
    function formatRupiah(angka, prefix = 'Rp. ') {
        let number_string = angka.toString().replace(/[^,\d]/g, ''), split = number_string.split(','), sisa = split[0].length % 3, rupiah = split[0].substr(0, sisa), ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return prefix + rupiah;
    }

    // Format Rupiah otomatis pada total_payment
    totalInput.addEventListener('input', function () {
        const raw = this.value.replace(/[^\d]/g, ''); // Menghapus karakter selain angka
        this.value = formatRupiah(raw);
    });

    // Set default view saat halaman dimuat
    toggleMemberFields(memberSelect.value);
});

document.querySelector('form').addEventListener('submit', function (e) {
    const totalInput = document.getElementById('total_payment');
    const rawTotal = totalInput.value.replace(/[^\d]/g, ''); // Remove all non-numeric characters
    totalInput.value = rawTotal; // Update the field with the numeric value
});
</script>
@endpush
