@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
    {{-- Backdrop agar produk “membelakangi” modal --}}
    <div class="modal-backdrop fade show"></div>

    {{-- Modal langsung terbuka --}}
    <div class="modal show d-block" tabindex="-1" role="dialog" style="overflow-y: auto;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow rounded-3" style="max-width: 600px; margin: auto; font-size: 14px;">

                {{-- Header tanpa border bawah --}}
                <div class="modal-header border-0">
                    <h5 class="modal-title">Detail Pembelian</h5>
                    <a href="{{ url()->previous() }}" class="btn-close"></a>
                </div>

                <div class="modal-body">
                    {{-- Info Member --}}
                    <div class="mb-3" style="line-height: 1.5;">
                        <p><span class="fw-semibold">Member Status :</span> {{ $purchase->member ? 'Member' : 'Bukan Member' }}</p>
                        <p><span class="fw-semibold">No. HP :</span> {{ optional($purchase->member)->no_phone ?? '-' }}</p>
                        <p><span class="fw-semibold">Bergabung Sejak :</span> {{ optional($purchase->member)->created_at?->format('d F Y') ?? '-' }}</p>
                        <p><span class="fw-semibold">Poin Member :</span> {{ optional($purchase->member)->poin ?? 0 }}</p>
                    </div>

                    {{-- Daftar Produk --}}
                    <table class="table table-borderless mb-3" style="font-size: 14px;">
                        <thead class="border-bottom fw-semibold">
                            <tr>
                                <th class="text-start">Nama Produk</th>
                                <th>QTY</th>
                                <th>Harga</th>
                                <th>Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->details as $item)
                                <tr class="border-bottom">
                                    <td class="text-start">{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Total --}}
                    <div class="d-flex justify-content-end fw-semibold mb-3">
                        <span class="me-2">Total</span>
                        <span class="fw-bold">Rp. {{ number_format($purchase->total_after_points ?? $purchase->total_price, 0, ',', '.') }}</span>
                    </div>

                    {{-- Footer info --}}
                    <div class="text-center text-muted small" style="font-size: 12px; line-height: 1.3;">
                        Dibuat pada : {{ $purchase->created_at->format('Y-m-d H:i:s') }}<br>
                        Oleh : {{ ucfirst($purchase->user->role) }}
                    </div>
                </div>

                {{-- Footer tanpa border atas, dengan satu tombol --}}
                <div class="modal-footer border-0">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">Tutup</a>
                </div>
            </div>
        </div>
    </div>
@endsection
