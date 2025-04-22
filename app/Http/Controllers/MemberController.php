<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Menampilkan semua member
    public function index()
    {
        // Retrieve product items from the session
        $productItems = session('selected_products', []);


        // Retrieve the stored member phone from the session
        $memberPhone = session('member_phone_checkout');
        $total_payment = session('total_payment_checkout');
        // dd($total_payment);
        // dd($total_payment);
        // Ambil hanya product_id dari item yang jumlahnya > 0
        $productIds = collect($productItems)
            ->filter(fn($item) => isset($item['jumlah']) && $item['jumlah'] > 0 && isset($item['product_id']))
            ->pluck('product_id')
            ->map(fn($id) => (int) $id) // pastikan integer
            ->all();

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

        // Retrieve member data based on phone number from the session
        $memberData = null;
        if ($memberPhone) {
            $memberData = Member::where('no_phone', $memberPhone)->first(['id', 'name','poin']);
        }

        $user = auth()->user();
        $hasPurchasedBefore = $user->purchases()->exists();

        // dd($total_payment);

        return view('pembelian.member', [
            'products' => $productsWithQuantity,
            'hasPurchasedBefore' => $hasPurchasedBefore,
            'memberPhone' => $memberPhone, // Pass member phone to the view
            'memberData' => $memberData, // Pass member data to the view
            'total_payment' => $total_payment,
        ]);
    }


    public function checkMember(Request $request)
{
    $phone = $request->input('no_phone');

    // Mencari member berdasarkan nomor telepon
    $member = Member::where('no_phone', $phone)->first();

    if ($member) {
        return response()->json(['status' => 'found', 'member_name' => $member->name, 'member_id' => $member->id]);
    } else {
        return response()->json(['status' => 'not_found']);
    }
}

public function showInvoice()
{
    $productItems = session('selected_products', []);
    // dd($productItems);
    // Ambil data pembelian dari session
    $purchaseData = session('purchase_data');
    // dd($purchaseData);

    if (!$purchaseData) {
        return redirect()->route(auth()->user()->role . '.pembelian.index')->withErrors('Data pembelian tidak ditemukan!');
    }

    return view('pembelian.struk', compact('purchaseData', 'productItems'));
}



}
