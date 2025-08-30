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
    public function showForm(Request $request,$id)
    {
        $lot = Lot::findOrFail($id);
        if ($request->session()->get('inquiry_success_for_lot_' . $lot->id) || Auth::check()) {
            $lot->load('product');
            return view('public.lots', compact('lot'));
        }
        return view('public.form', compact('lot'));
    }

    /**
     * Handle the submission of the public form.
     */
    public function handleForm(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'address' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        LotInquiry::create([
            'lot_id' => $id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
        ]);

        $request->session()->put('inquiry_success_for_lot_' . $id, true);

        return redirect()->route('public.form', $id)
                         ->with('success', 'Thank you! You can now view the lot details.');
    }
}
