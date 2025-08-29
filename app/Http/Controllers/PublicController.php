<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Lot;
use App\Models\LotInquiry;
use Illuminate\Support\Facades\Validator;
Use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function showForm(Request $request, Lot $lot)
    {
        if ($request->session()->get('inquiry_success_for_lot_' . $lot->id) || Auth::check()) {
            $lot->load('product');
            return view('public.lots', compact('lot'));
        }


        // Otherwise, show the inquiry form
        return view('public.form', compact('lot'));
    }

    /**
     * Handle the submission of the public form.
     */
    public function handleForm(Request $request, Lot $lot)
    {
        // 1. Validate the incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'address' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. Store the inquiry data in the database
        LotInquiry::create([
            'product_id' => $product->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
        ]);

        // 3. Set a session flag to indicate successful submission for this specific lot
        $request->session()->put('inquiry_success_for_lot_' . $lot->id, true);

        // 4. Redirect back to the same URL, which will now show the lot details
        return redirect()->route('public.form', $lot->id)
                         ->with('success', 'Thank you! You can now view the lot details.');
    }
}
