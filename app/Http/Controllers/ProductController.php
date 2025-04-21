<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Tampilkan semua produk
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $query = Product::query();

        // Role-based: batasi kolom jika petugas
        if ($user->role === 'petugas') {
            $query->select('id', 'name', 'stock', 'price', 'image');
        } elseif ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Search filter
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Ambil data
        $products = $query->latest()->paginate(10)->withQueryString();


        // Kirim ke view
        return view('produk.index', compact('products', 'search'));
    }



    // Tampilkan detail produk (untuk petugas)
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('produk.show', compact('product'));
    }

    // Tampilkan form tambah produk
    public function create()
    {
        return view('produk.create');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName(); // Bisa juga pakai uniqid() biar unik
            $file->move(public_path('image/produk'), $filename); // Simpan ke folder public/image/produk
            $data['image'] = 'image/produk/' . $filename; // Simpan path relatif ke database
        }


        Product::create($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // Update produk
    public function update(Request $request, $id)
    {
        $produk = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'stock' => 'required|integer',
            'price' => 'required|integer',
            'image' => 'nullable|image|max:2048',
        ]);

        // Hapus image lama jika diganti
        if ($request->hasFile('image')) {
            if ($produk->image && file_exists(public_path($produk->image))) {
                unlink(public_path($produk->image)); // Hapus file lama dari public/image/produk
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('image/produk'), $filename);
            $data['image'] = 'image/produk/' . $filename;
        }


        $produk->update($data);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui!');
    }

    // Hapus produk
    public function destroy($id)
    {
        $produk = Product::findOrFail($id);

        if ($produk->image) {
            Storage::disk('public')->delete($produk->image);
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus!');
    }
    // Tampilkan form edit produk
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('produk.edit', compact('product'));
    }

    // Update stock saja
    public function updateStock(Request $request, $id)
    {
        $produk = Product::findOrFail($id);

        $data = $request->validate([
            'stock' => 'required|integer',
        ]);

        $produk->update($data);

        return redirect()->back()->with('success', 'Stok produk berhasil diperbarui!');
    }

}
