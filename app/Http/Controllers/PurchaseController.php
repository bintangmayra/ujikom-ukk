<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseExport;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Member;
use App\Models\PurchaseDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembelianExport;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Purchase::with(['user', 'member']);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('member', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                   ->orWhere('no_phone', 'like', '%' . $search . '%');
            })
            ->orWhere('id', 'like', '%' . $search . '%'); // pakai ID transaksi sebagai alternatif
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('pembelian.index', compact('purchases', 'search'));
    }



    public function create()
    {
        $products = Product::all();
        return view('pembelian.select-products', compact('products'));
    }

    public function checkout(Request $request)
    {
        $productIds = $request->input('products', []);
        session(['selected_products' => $productIds]);
        // dd($productIds);
        return redirect()->route(auth()->user()->role . '.pembelian.checkout.page');
    }

    public function checkoutPage(Request $request)
    {
        $productItems = session('selected_products', []);
        // dd($productItems);

        // Ambil hanya product_id dari item yang jumlahnya > 0
        $validProductItems = collect($productItems)
            ->filter(fn($item) => isset($item['product_id']) && isset($item['jumlah']) && $item['jumlah'] > 0);

        // dd($validProductItems);
        $productIds = $validProductItems
            ->pluck('product_id')
            ->map(fn($id) => (int) $id)
            ->all();

        // dd($productIds);



        // Ambil data produk dari database
        $products = Product::whereIn('id', $productIds)->get();

        // Gabungkan jumlah dengan produk berdasarkan product_id
        $productsWithQuantity = $products->map(function ($product) use ($productItems) {
            $productItem = collect($productItems)->first(function ($item) use ($product) {
                return isset($item['product_id']) && (int) $item['product_id'] === $product->id;
            });

            $product->jumlah = $productItem ? $productItem['jumlah'] : 0;

            return $product;
        });

        $user = auth()->user();
        $hasPurchasedBefore = $user->purchases()->exists();

        // dd($productsWithQuantity);

        return view('pembelian.checkout', [
            'products' => $productsWithQuantity,
            'hasPurchasedBefore' => $hasPurchasedBefore,
        ]);
    }

    public function store(Request $request)
    {
        $productItems = session('selected_products', []);

        $cleanTotalPayment = preg_replace('/[^0-9]/', '', $request->total_payment);
        $cleanTotalPayment = (int) $cleanTotalPayment;
        $request->merge(['total_payment' => $cleanTotalPayment]);

        if (
            (is_null($request->input('member_id')) || $request->input('member_id') == '') &&
            (is_null($request->input('member_name')) || $request->input('member_name') == '')
        ) {
            if ($request->filled('member_phone')) {
                session([
                    'products_checkout' => $request->products,
                    'total_payment_checkout' => $request->total_payment,
                    'member_phone_checkout' => $request->member_phone,
                    'member_no_checkout' => $request->member_no,
                ]);
                return redirect()->route(auth()->user()->role . '.member')->withInput();
            }
        }

        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'member_id' => 'nullable|exists:members,id',
            'status_member' => 'required|in:member,non-member',
            'total_payment' => 'required|integer|min:0',
            'member_phone' => 'nullable|regex:/^[0-9]+$/',
            'member_no' => 'nullable|string',
        ]);

        $memberId = null;
        $pointsToUse = 0;
        $pointValue = 0.01; // 1% dari total harga

        $products = Product::whereIn('id', $request->products)->get();
        $totalPrice = $products->sum(function ($product) use ($productItems) {
            $qty = collect($productItems)->firstWhere('product_id', $product->id)['jumlah'] ?? 1;
            return $product->price * $qty;
        });

        if ($request->status_member === 'member' && $request->filled('member_phone')) {
            $member = Member::where('no_phone', $request->member_phone)->first();

            if (!$member) {
                $member = Member::create([
                    'name' => $request->member_name ?? 'Nama Tidak Diketahui',
                    'no_phone' => $request->member_phone,
                    'poin' => 0,
                ]);
            }

            $memberId = $member->id;

            // Hitung maksimum potongan dari poin
            $maxDiscount = $member->poin * $pointValue;

            // Jika total harga lebih kecil dari potongan, batasi poin yang dipakai
            if ($totalPrice < $maxDiscount) {
                $pointsToUse = floor($totalPrice * $pointValue); // Menggunakan poin 1% dari total harga
            } else {
                $pointsToUse = $member->poin;
            }

            $discountFromPoints = $pointsToUse * $pointValue;
            $finalTotal = max(0, $totalPrice - $discountFromPoints); // Harga setelah poin (Rp. 891.000)

            // Tambah poin jika belanja (sebelum potongan) lebih dari 100.000
            $pointsToAdd = $totalPrice > 100000 ? floor($totalPrice * $pointValue) : 0;

            // Update poin member
            $member->poin = max(0, $member->poin + $pointsToAdd - $pointsToUse);
            $member->save();

            $request->merge(['member_id' => $member->id]);
        } else {
            $finalTotal = $totalPrice;
        }

        // Total bayar tetap Rp. 900.000 (tidak dipengaruhi poin)
        $totalPayment = $cleanTotalPayment;

        // Menghitung kembalian berdasarkan total pembayaran yang sudah bersih dari poin
        $change = $totalPayment - $totalPrice;  // Kembalian dihitung dari harga sebelum poin

        if ($change < 0) {
            return back()->withErrors('Jumlah pembayaran kurang dari total harga setelah potongan poin!')->withInput();
        }

        // Simpan pembelian
        $purchase = Purchase::create([
            'user_id' => auth()->id(),
            'member_id' => $memberId,
            'total_price' => $totalPrice,
            'total_payment' => $cleanTotalPayment,
            'change' => $change,
        ]);

        // Simpan detail pembelian
        foreach ($products as $product) {
            $quantity = collect($productItems)->firstWhere('product_id', $product->id)['jumlah'] ?? 1;

            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);

            $product->decrement('stock', $quantity);
        }

        // Simpan data ke session untuk invoice
        session([
            'purchase_data' => [
                'id' => $purchase->id,
                'member' => $purchase->member,
                'products' => $purchase->products,
                'total_price' => $totalPrice,
                'total_payment' => $cleanTotalPayment,
                'change' => $change,
                'use_points' => $pointsToUse,
                'final_total' => $finalTotal, // Harga setelah poin, Rp. 891.000
                'created_at' => $purchase->created_at,
                'user_role' => $purchase->user->role,
            ]
        ]);

        return redirect()->route(auth()->user()->role . '.invoice')->with('success', 'Pembelian berhasil disimpan!');
    }



    public function show($id)
    {

        $purchase = Purchase::with(['details.product', 'user', 'member'])->findOrFail($id);
        return view('pembelian.show', compact('purchase'));
    }

    public function download($id)
    {
        // Ambil data pembelian berdasarkan id
        $purchaseData = Purchase::with('products')->findOrFail($id);  // Ambil data pembelian dan produk terkait

        // dd($purchaseData);
        // Ambil data produk yang dipilih dari session
        $productItems = session('selected_products', []);

        $purchaseData1 = session('purchase_data');

        // dd($purchaseData);


        // Jika data pembelian tidak ditemukan, redirect dengan pesan error
        if (!$purchaseData) {
            return redirect()->route(auth()->user()->role . '.pembelian.index')->withErrors('Data pembelian tidak ditemukan!');
        }

        // Kirimkan purchaseData dan productItems ke view untuk menghasilkan PDF
        $pdf = Pdf::loadView('pembelian.invoice', compact('purchaseData', 'productItems', 'purchaseData1'));

        // Mengunduh PDF dengan nama file berdasarkan ID pembelian
        return $pdf->download('bukti-pembelian-' . $purchaseData->id . '.pdf');
    }



    public function export()
    {
        return Excel::download(new PurchaseExport, 'data-penjualan.xlsx');
    }


}
