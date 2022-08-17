<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    //
    public function index()
    {
        $produk = Produk::latest()->paginate(5);

        return view('produk.index', compact('produk'));
    }
    public function create()
    {
        return view('produk.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'     => 'required|min:5',
            'description'   => 'required|min:10',
            'price' => 'numeric'
        ]);

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/produks', $image->hashName());

        //create post
        Produk::create([
            'image'     => $image->hashName(),
            'name'     => $request->name,
            'description'   => $request->description,
            'price' => $request->price
        ]);

        //redirect to index
        return redirect()->route('produk.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }
    public function edit(produk $produk)
    {
        return view('produk.edit', compact('produk'));
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Produk $produk)
    {
        //validate form
        $this->validate($request, [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'     => 'required|min:5',
            'description'   => 'required|min:10',
            'price' => 'numeric'
        ]);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/produks', $image->hashName());

            //delete old image
            Storage::delete('public/produks/'.$produk->image);

            //update post with new image
            $produk->update([
                'image'     => $image->hashName(),
                'name'     => $request->name,
                'description'   => $request->description,
                'price' => $request->price
            ]);

        } else {

            //update post without image
            $produk->update([
                'name'     => $request->name,
                'description'   => $request->description,
                'price' => $request->price
            ]);
        }

        //redirect to index
        return redirect()->route('produk.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy(Produk $produk)
    {
        //delete image
        Storage::delete('public/produks'. $produk->image);

        //delete post
        $produk->delete();

        //redirect to index
        return redirect()->route('produk.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
