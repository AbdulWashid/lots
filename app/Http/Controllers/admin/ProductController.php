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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lots.*.number' => 'required|string|max:255',
            'lots.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
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

            DB::commit(); // Commit the transaction

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            // Optionally log the error: Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to create product. Please try again.')->withInput();
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lots.*.number' => 'required|string|max:255',
            'lots.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $productData = $request->only('name');

            // Handle image update
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $productData['image'] = $request->file('image')->store('products', 'public');
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

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');

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
        DB::beginTransaction();
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')->with('error', 'Failed to delete product.');
        }
    }
    public function fetchLots(Product $product)
    {
        // Eager load the lots and return them as JSON
        $product->load('lots');
        return response()->json($product->lots);
    }
    public function displayQrImage(Lot $lot)
    {
        // Generate the URL that the QR code will point to
        $url = route('public.form', $lot->id);

        // Generate the QR code as a PNG image stream
        $qrCode = QrCode::format('png')->size(300)->generate($url);

        // Return the image as a response without forcing a download
        return Response::make($qrCode, 200, ['Content-Type' => 'image/png']);
    }
    public function downloadQr(Product $product)
    {
        $url = route('public.form', $product->id);
        $qrCode = QrCode::format('png')->size(300)->generate($url);
        $headers = [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="product_qr_' . $product->id . '.png"',
        ];
        return response($qrCode, 200, $headers);
    }
}
