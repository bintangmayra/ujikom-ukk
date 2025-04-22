@extends('layouts.app')
@section('title', 'Checkout Pembelian')

@section('content')
    <div class="container">
        <h2>Bukti Pembelian</h2>
        <hr>

        <h4>Member Status : {{ $purchaseData['member'] ? 'Member' : 'Bukan Member' }}</h4>
        <p>
            No. HP : {{ $purchaseData['member']->no_phone ?? '-' }}<br>
            Bergabung Sejak : {{ $purchaseData['member'] ? \Carbon\Carbon::parse($purchaseData['member']->created_at)->format('d M Y') : '-' }}<br>
            Poin Member : {{ $purchaseData['member']->poin ?? '-' }}
        </p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>QTY</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseData['products'] as $product)
                    @php
                        $selectedProduct = collect($productItems)->firstWhere('product_id', $product->id);
                        $quantity = $selectedProduct ? $selectedProduct['jumlah'] : 0;
                        $productPrice = $selectedProduct ? $selectedProduct['product_price'] : 0;
                    @endphp
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $quantity }}</td>
                        <td>Rp. {{ number_format($productPrice, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($productPrice * $quantity, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table table-bordered">
            <tr>
                <th>Total Harga</th>
                <td class="text-right">Rp. {{ number_format($purchaseData['total_price'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Poin Digunakan</th>
                <td class="text-right">{{ $purchaseData['use_points'] }}</td>
            </tr>
            @if ($purchaseData['member'])
            <tr>
                <th>Harga Setelah Poin</th>
                <td class="text-right">Rp. {{ number_format($purchaseData['final_total'], 0, ',', '.') }}</td>
            </tr>
        @endif
        
            <tr>
                <th>Harga Total Bayar</th>
                <td class="text-right">Rp. {{ number_format($purchaseData['total_payment'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Kembalian</th>
                <td class="text-right">Rp. {{ number_format($purchaseData['change'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <p class="text-right">
            {{ \Carbon\Carbon::parse($purchaseData['created_at'])->format('d-m-Y H:i') }} | {{ $purchaseData['user_role'] }}
        </p>

        <!-- Button trigger modal -->
        <div class="text-end mb-3">
            <a href="{{ route(auth()->user()->role . '.pembelian.index') }}" class="btn btn-primary me-2">
                Kembali ke Pembelian
            </a>
            <a href="{{ route('petugas.pembelian.download', $purchaseData['id']) }}" class="btn btn-primary">
                Unduh Bukti
            </a>
            <!-- Button untuk menampilkan popup -->
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#confirmationModal">
                Lihat Detail Pembelian
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmationModalLabel">Detail Pembelian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Member Status:</strong> {{ $purchaseData['member'] ? 'Member' : 'Bukan Member' }}</p>
                        <p>No. HP: {{ $purchaseData['member']->no_phone ?? '-' }}</p>
                        <p>Bergabung Sejak: {{ $purchaseData['member'] ? \Carbon\Carbon::parse($purchaseData['member']->created_at)->format('d M Y') : '-' }}</p>
                        <p>Poin Member: {{ $purchaseData['member']->poin ?? '-' }}</p>

                        <!-- Table inside modal -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>QTY</th>
                                    <th>Harga</th>
                                    <th>Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseData['products'] as $product)    
                                    @php
                                        $selectedProduct = collect($productItems)->firstWhere('product_id', $product->id);
                                        $quantity = $selectedProduct ? $selectedProduct['jumlah'] : 0;
                                        $productPrice = $selectedProduct ? $selectedProduct['product_price'] : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $quantity }}</td>
                                        <td>Rp. {{ number_format($productPrice, 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($productPrice * $quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@endpush
