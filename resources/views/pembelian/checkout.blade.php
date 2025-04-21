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
        $total_price = $products->sum(function($product) {
            return $product->price * $product->jumlah;
        });

        $canUsePoints = $memberData && $memberData->poin;
        $use_points_value = $canUsePoints ? 5000 : 0;
        $final_total = $total_price - $use_points_value;
    @endphp

    <form action="{{ route(auth()->user()->role . '.pembelian.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Kolom Kiri --}}
            <div class="col-md-6">
                <h6>Produk yang Dipilih</h6>
                <ul class="list-group mb-3">
                    @foreach($products as $product)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $product->name }} x {{ $product->jumlah }}</span>
                            <span>Rp {{ number_format($product->price * $product->jumlah, 0, ',', '.') }}</span>
                        </li>
                        <input type="hidden" name="products[]" value="{{ $product->id }}">
                    @endforeach

                    <li class="list-group-item d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                    </li>

                    @if ($canUsePoints)
                        <li class="list-group-item d-flex justify-content-between text-success">
                            <span>Diskon Poin</span>
                            <span>- Rp {{ number_format($use_points_value, 0, ',', '.') }}</span>
                        </li>
                    @endif

                    <li class="list-group-item d-flex justify-content-between fw-bold bg-light">
                        <span>Total</span>
                        <span>Rp {{ number_format($final_total, 0, ',', '.') }}</span>
                    </li>
                </ul>

                <input type="hidden" name="total_price" value="{{ $total_price }}">
            </div>

            {{-- Kolom Kanan --}}
            <div class="col-md-6">
                <input type="hidden" name="status_member" value="member">

                <div class="mb-3">
                    <label for="member_phone" class="form-label">No Telepon</label>
                    <input type="text" id="member_phone" name="member_phone" class="form-control" placeholder="No Telepon" value="{{ $memberPhone }}">
                </div>

                <div class="mb-3">
                    <label for="member_name" class="form-label">Nama Member</label>
                    <input type="text" id="member_name" name="member_name" class="form-control" placeholder="Nama Member" value="{{ $memberData ? $memberData->name : '' }}">
                    <input type="hidden" name="member_id" id="member_id" value="{{ $memberData ? $memberData->id : '' }}">
                </div>

                @if ($canUsePoints)
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="use_points" name="use_points" value="50">
                        <label class="form-check-label" for="use_points">Gunakan Poin Member (50 poin = Rp 5.000)</label>
                    </div>
                @else
                    <div class="alert alert-info">
                        Anda belum dapat menggunakan poin karena ini adalah pembelian pertama atau belum memiliki poin.
                    </div>
                @endif

                <div class="mb-3">
                    <label for="total_payment" class="form-label">Jumlah Pembayaran</label>
                    <input type="text" class="form-control" id="total_payment" name="total_payment" placeholder="Contoh: Rp 50.000" required>
                </div>

                <button type="submit" class="btn btn-success w-100 mt-3">Selesaikan Pembelian</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalInput = document.getElementById('total_payment');

        totalInput.addEventListener('input', function () {
            const raw = this.value.replace(/[^\d]/g, '');
            this.value = formatRupiah(raw);
        });

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

        // Clean total_payment input saat submit
        document.querySelector('form').addEventListener('submit', function (e) {
            const totalInput = document.getElementById('total_payment');
            if (totalInput) {
                const rawTotal = totalInput.value.replace(/[^\d]/g, '');
                totalInput.value = rawTotal;
            }
        });
    });
</script>
@endpush
