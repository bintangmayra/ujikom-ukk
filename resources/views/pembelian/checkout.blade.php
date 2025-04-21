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
        $total_price = 0;
        foreach ($products as $key => $product) {
            $total_price += ((int)$product['price'] * (int)$product['jumlah']);
        }
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
                            <span>{{ $product['name'] }} X {{ $product['jumlah'] }}</span>
                            <span>Rp {{ number_format($product['price'] * $product['jumlah'], 0, ',', '.') }}</span>
                        </li>
                        <input type="hidden" name="products[]" value="{{ $product['id'] }}">
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

                {{-- Detail Member --}}
                <div id="memberDetail" style="display: none;">
                    <div class="mb-3">
                        <label for="member_phone" class="form-label">No. Telepon</label>
                        <input
                            type="text"
                            class="form-control"
                            id="member_phone"
                            name="member_phone"
                            value="{{ old('member_phone', auth()->user()->member_phone) }}"
                        >
                    </div>
                </div>

                {{-- Hidden Member ID --}}
                <input type="hidden" name="member_id" id="member_id">

                {{-- Total Bayar --}}
                <label for="total_payment" class="form-label">Total Bayar</label>
                <input
                    type="text"
                    id="total_payment"
                    name="total_payment"
                    class="form-control"
                    placeholder="Masukkan nominal pembayaran"
                    required
                    autocomplete="off"
                >

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
    const totalInput = document.getElementById('total_payment');
    const memberPhoneInput = document.getElementById('member_phone');
    const memberSelect = document.getElementById('status_member');
    const memberDetail = document.getElementById('memberDetail');

    // Menampilkan/Sembunyikan field detail member
    function toggleMemberFields(status) {
        memberDetail.style.display = (status === 'member') ? 'block' : 'none';
    }

    memberSelect.addEventListener('change', function () {
        toggleMemberFields(this.value);
    });

    // Format input ke format rupiah
    function formatRupiah(angka, prefix = 'Rp. ') {
        let number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        return prefix + rupiah;
    }

    totalInput.addEventListener('input', function () {
        const raw = this.value.replace(/[^\d]/g, '');
        this.value = formatRupiah(raw);
    });

    // Default saat load
    toggleMemberFields(memberSelect.value);
});

// Hapus format rupiah saat submit
document.querySelector('form').addEventListener('submit', function (e) {
    const totalInput = document.getElementById('total_payment');
    const rawTotal = totalInput.value.replace(/[^\d]/g, '');
    totalInput.value = rawTotal;
});
</script>
@endpush
