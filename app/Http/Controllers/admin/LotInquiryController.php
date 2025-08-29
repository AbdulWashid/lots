<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LotInquiry;

class LotInquiryController extends Controller
{
    public function index()
    {
        // Fetch all inquiries, eager load the related product to prevent N+1 issues.
        // Paginate the results to show 15 per page.
        $inquiries = LotInquiry::with('product')->latest()->paginate(15);

        return view('admin.lot_inquiries.index', compact('inquiries'));
    }
    public function destroy(LotInquiry $lotInquiry)
    {
        try {
            $lotInquiry->delete();
            return redirect()->route('admin.lot-inquiries.index')->with('success', 'Inquiry deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.lot-inquiries.index')->with('error', 'Failed to delete inquiry.');
        }
    }
}
