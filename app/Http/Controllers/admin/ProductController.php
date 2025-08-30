<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Lot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        $products = $query->latest()->paginate(10);

        return view('admin.products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name',
            'image' => 'nullable|string', // Correct for Base64
            'lots.*.number' => 'required|string|max:255',
            'lots.*.description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $imagePath = null;
            // CORRECTED: Check if the 'image' input string is present, not if it's a file.
            if ($request->filled('image')) {
                // 1. Get the Base64 string from the request
                $base64Image = $request->image;

                // 2. Decode the Base64 string
                @list($type, $data) = explode(';', $base64Image);
                @list(, $data)      = explode(',', $data);
                $imageData = base64_decode($data);

                // 3. Generate a unique filename
                $imageName = Str::random(20) . '.jpg';

                // 4. Save the image to storage
                Storage::disk('public')->put('products/' . $imageName, $imageData);
                $imagePath = 'products/' . $imageName;
            }

            $product = Product::create([
                'name' => $request->name,
                'image' => $imagePath,
            ]);

            if ($request->has('lots')) {
                foreach ($request->lots as $lotData) {
                    $product->lots()->create([
                        'number' => $lotData['number'],
                        'description' => $lotData['description'],
                    ]);
                }
            }

            DB::commit();
            toastr()->success('Product created successfully.');
            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Failed to create product. Please try again.');
            return redirect()->back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('lots');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('lots');
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'image' => 'nullable|string', // Correct for Base64
            'lots.*.number' => 'required|string|max:255',
            'lots.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $productData = $request->only('name');

            // CORRECTED: Handle image update from Base64 string
            if ($request->filled('image')) {
                // Delete the old image if it exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                // Decode and save the new Base64 image
                $base64Image = $request->image;
                @list($type, $data) = explode(';', $base64Image);
                @list(, $data)      = explode(',', $data);
                $imageData = base64_decode($data);
                $imageName = Str::random(20) . '.jpg';
                Storage::disk('public')->put('products/' . $imageName, $imageData);
                $productData['image'] = 'products/' . $imageName;
            }

            $product->update($productData);

            // Sync lots: Delete old lots and create new ones
            $product->lots()->delete();
            if ($request->has('lots')) {
                foreach ($request->lots as $lotData) {
                    $product->lots()->create([
                        'number' => $lotData['number'],
                        'description' => $lotData['description'],
                    ]);
                }
            }

            DB::commit();
            toastr()->success('Product updated successfully.');
            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product. Please try again.')->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id); // Find the product first

        DB::beginTransaction();
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            DB::commit();
            toastr()->success('Product deleted successfully.');
            return redirect()->route('admin.products.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Failed to delete product.');
            return redirect()->route('admin.products.index');
        }
    }
    public function fetchLots(Product $product)
    {
        $product->load('lots');
        return response()->json($product->lots);
    }
    public function displayQrImage(Lot $lot)
    {
        $url = route('public.form', $lot->id);
        $qrCode = QrCode::format('svg')->size(300)->generate($url);
        return response($qrCode, 200, ['Content-Type' => 'image/svg+xml']);
    }
    public function downloadQr(Lot $lot)
    {
        $url = route('public.form', $lot->id);
        $qrCode = QrCode::format('svg')->size(300)->generate($url);
        $headers = [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="lot_qr_' . $lot->id . '.svg"',
        ];
        return response($qrCode, 200, $headers);
    }
}
