<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalPenjualan = null;
        $chartData = [];
        $totalPerProduk = [];

        if ($user->role === 'petugas') {
            $totalPenjualan = Purchase::whereDate('created_at', Carbon::today())->count();
        }

        if ($user->role === 'admin') {
            // Data untuk grafik bar (penjualan per tanggal dan produk 7 hari terakhir)
            $salesPerDayRaw = DB::table('purchase_details')
                ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
                ->join('products', 'purchase_details.product_id', '=', 'products.id')
                ->select(
                    DB::raw('DATE(purchases.created_at) as tanggal'),
                    'products.name',
                    DB::raw('SUM(purchase_details.quantity) as total')
                )
                ->whereDate('purchases.created_at', '>=', Carbon::now()->subDays(6)->toDateString())
                ->groupBy('tanggal', 'products.name')
                ->orderBy('tanggal')
                ->get()
                ->groupBy('tanggal');

            foreach ($salesPerDayRaw as $tanggal => $items) {
                $entry = ['tanggal' => $tanggal];
                foreach ($items as $item) {
                    $entry[$item->name] = $item->total;
                }
                $chartData[] = $entry;
            }

            // Data untuk grafik pie (total penjualan per produk)
            $totalPerProduk = DB::table('purchase_details')
                ->join('products', 'purchase_details.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(purchase_details.quantity) as total'))
                ->groupBy('products.name')
                ->pluck('total', 'products.name')
                ->toArray();
        }

        return view('dashboard', compact('user', 'totalPenjualan', 'chartData', 'totalPerProduk'));
    }
}
