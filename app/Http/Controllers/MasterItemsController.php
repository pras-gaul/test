<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Models\Category;
use Illuminate\Http\Request;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $kode = $request->kode;
        $nama = $request->nama;
        $hargamin = $request->hargamin;
        $hargamax = $request->hargamax;

        $data_search = MasterItem::query();

        if (!empty($kode)) $data_search = $data_search->where('kode', $kode);
        if (!empty($nama)) $data_search = $data_search->where('nama', 'LIKE', '%' . $nama . '%');
        if (!empty($hargamin)) $data_search = $data_search->where('harga_beli', '>=', $hargamin)->where('harga_beli', '<=', $hargamax);

        $data_search = $data_search->select('kode', 'nama','kategori', 'harga_beli', 'laba', 'supplier')->orderBy('id')->get();

        return json_encode([
            'status' => 200,
            'data' => $data_search
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = [];
        } else {
            $item = MasterItem::find($id);
        }

        // 2. Ambil SEMUA KATEGORI dari database
        $allCategories = Category::all();

        $data['item'] = $item;
        $data['method'] = $method;

        // 3. Tambahkan $allCategories ke array data
        $data['allCategories'] = $allCategories;

        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::where('kode', $kode)->first();
        return view('master_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        if ($method == 'new') {
            $data_item = new MasterItem;
            $kode = MasterItem::count('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
            sleep(3);
        } else {
            $data_item = MasterItem::find($id);
            $kode = $data_item->kode;
        }

         $request->validate([
        'foto' => 'image|mimes:jpg,jpeg,png|max:2048',
        'categories' => 'required|array', 
        'categories.*' => 'exists:categories,id',
        ]);

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        //$data_item->jenis = $request->jenis;

        if ($request->hasFile('foto')) {
        $filename = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('uploads'), $filename);
        $data_item->foto = $filename;
}
        $data_item->save();
        $data_item->categories()->sync($request->categories);

        return redirect('master-items');
    }


    public function delete($id)
    {
        MasterItem::find($id)->delete();
        return redirect('master-items');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach($data as $item)
        {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100,1000000);
            $item->laba = rand(10,99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            //$item->jenis = $this->getRandomJenis();
            
            $item->save();   
            // 3. Sinkronisasi Relasi Many-to-Many dengan Kategori Acak
            $randomCategoryIds = $this->getRandomCategoryIds();
            $item->categories()->sync($randomCategoryIds);
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi','Bukulapuk','TokoBagas','E Commurz','Blublu'];
        $random = rand(0,4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat','Alkes','Matkes','Umum','ATK'];
        $random = rand(0,4);
        return $array[$random];
    }

protected $categoryIds = []; 

public function __construct()
{
    // Ambil semua ID kategori saat controller diinisialisasi
    $this->categoryIds = Category::pluck('id')->toArray();
}

/**
 * Mendapatkan satu atau lebih ID kategori acak.
 * @return array
 */
protected function getRandomCategoryIds()
{
    if (empty($this->categoryIds)) {
        return ['Obat','Alkes','Matkes','Umum','ATK'];
    }
    
    // Secara acak memilih 1 hingga 3 kategori
    $numToSelect = rand(1, min(3, count($this->categoryIds)));
    
    // Ambil kunci array acak
    $randomKeys = (array) array_rand($this->categoryIds, $numToSelect);
    
    $selectedIds = [];
    foreach ($randomKeys as $key) {
        $selectedIds[] = $this->categoryIds[$key];
    }
    
    return $selectedIds;
}

}
