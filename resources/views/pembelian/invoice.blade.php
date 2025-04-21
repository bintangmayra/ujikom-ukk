<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pembelian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; border: 1px solid #000; text-align: left; }
        h2, h4 { margin: 0 0 10px 0; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

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
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseData['products'] as $product)
                    @php
                        $selectedProduct = collect($productItems)->firstWhere('product_id', $product->id);
                        $quantity = $selectedProduct['jumlah'] ?? 0;
                        $productPrice = $selectedProduct['product_price'] ?? 0;
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

            {{-- Tampilkan poin hanya jika member --}}
            @if ($purchaseData['member'])
                @if ($purchaseData['use_points'] > 0)
                    <tr>
                        <th>Poin Digunakan</th>
                        <td class="text-right">{{ $purchaseData['use_points'] }}</td>
                    </tr>
                @endif

                <tr>
                    <th>Harga Setelah Poin</th>
                    <td class="text-right">Rp. {{ number_format($purchaseData['final_total'], 0, ',', '.') }}</td>
                </tr>
            @endif

            <tr>
                <th>Total Bayar</th>
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

    </div>
</body>
</html>
