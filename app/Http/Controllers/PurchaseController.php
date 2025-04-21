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
    public function index()
    {
        $purchases = Purchase::with('user', 'member')->orderBy('created_at', 'desc')->paginate(10);
        return view('pembelian.index', compact('purchases'));
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
        // Debug: Check the raw request data
        // dd($request->all());
        $productItems = session('selected_products', []);
        // dd($productItems);
        // Clean the total_payment by removing "Rp." and non-numeric characters (except for dots or commas)
        $cleanTotalPayment = preg_replace('/[^0-9]/', '', $request->total_payment); // Hilangkan semua non-angka
        $cleanTotalPayment = (int) $cleanTotalPayment; // Konversi ke integer
        $request->merge(['total_payment' => $cleanTotalPayment]);

        // dd($request->total_payment);

        // Periksa apakah 'member_id' dan 'member_name' null atau kosong
        if (
            (is_null($request->input('member_id')) || $request->input('member_id') == '') &&
            (is_null($request->input('member_name')) || $request->input('member_name') == '')
        ) {
            // Jika kedua member_id dan member_name null atau kosong, lakukan logika berikut
            if ($request->filled('member_phone')) {
                session([
                    'products_checkout' => $request->products,
                    'total_payment_checkout' => $request->total_payment,
                    'member_phone_checkout' => $request->member_phone, // Store member phone
                    'member_no_checkout' => $request->member_no,
                ]);
                return redirect()->route(auth()->user()->role . '.member')->withInput();
            }
        }

        $total_final = $request->total_price - ($request->use_points * 100);

        // dd($total_final);

        // Validate the input
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'member_id' => 'nullable|exists:members,id',
            'status_member' => 'required|in:member,non-member',
            'total_payment' => 'required|integer|min:0',
            'member_phone' => 'nullable|regex:/^[0-9]+$/',
            'member_no' => 'nullable|string',
        ]);

        // Logika untuk member (create member jika perlu)
        $memberId = null;
        if ($request->status_member === 'member' && $request->filled('member_phone')) {
            $member = Member::where('no_phone', $request->member_phone)->first();

            if (!$member) {
                $newMember = Member::create([
                    'name' => $request->member_name ?? 'Nama Tidak Diketahui',
                    'no_phone' => $request->member_phone,
                    'poin' => 0,
                ]);
                $memberId = $newMember->id;
                $member = $newMember;
            } else {
                $memberId = $member->id;
            }

            // Tambahkan logika poin di sini
            $pointsToAdd = 0;
            $pointsToSubtract = 0;

            // Jika total_payment lebih dari 100.000 → Tambah 100 poin
            if ($request->total_payment > 100000) {
                $pointsToAdd = 100;
            }

            // Jika use_points diisi → Kurangi 50 poin
            if ($request->filled('use_points')) {
                $pointsToSubtract = 50;
            }

            // Update poin member
            $member->poin = max(0, ($member->poin ?? 0) + $pointsToAdd - $pointsToSubtract);
            $member->save();

            // Simpan member_id jika butuh digunakan
            $request->merge(['member_id' => $member->id]);
        }


        // Ambil data produk
        $products = Product::whereIn('id', $request->products)->get();
        $totalPrice = $request->input('total_price');
        // dd($totalPrice);
        $totalPayment = $cleanTotalPayment;
        $change = $totalPayment - $total_final;
        // dd($change);

        // Periksa apakah pembayaran mencukupi
        if ($change < 0) {
            return back()->withErrors('Jumlah pembayaran kurang dari total harga!')->withInput();
        }



        // Simpan data pembelian
        $purchase = Purchase::create([
            'user_id' => auth()->id(),
            'member_id' => $memberId,
            'total_price' => $totalPrice,
            'total_payment' => $totalPayment,
            'change' => $change,
        ]);
        foreach ($products as $product) {
            // Cari kuantitas produk di session
            $quantity = 0;
            foreach ($productItems as $item) {
                if (isset($item['product_id']) && $item['product_id'] == $product->id) {
                    $quantity = $item['jumlah']; // Ambil kuantitas dari session
                    break;
                }
            }
        }


        // dd($request->use_points);

        // Menambahkan produk ke detail pembelian dan mengurangi stok
        foreach ($products as $product) {
            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
            $product->decrement('stock');
        }
        session([
            'purchase_data' => [
                'id' => $purchase->id, 
                'member' => $purchase->member,
                'products' => $purchase->products,
                'total_price' => $purchase->total_price,
                'total_payment' => $cleanTotalPayment,
                'change' => $change,
                'use_points' => $request->use_points ?? 0,
                'final_total' => $total_final, // Asumsi 1 poin = 100
                'created_at' => $purchase->created_at,
                'user_role' => $purchase->user->role,
            ]
        ]);

        // Redirect dengan pesan sukses
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
