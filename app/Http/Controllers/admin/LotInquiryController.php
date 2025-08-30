<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LotInquiry;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LotInquiriesExport;
use Carbon\Carbon;

class LotInquiryController extends Controller
{
    public function index()
    {
        $inquiries = LotInquiry::join('lots', 'lot_inquiries.lot_id', '=', 'lots.id')
            ->join('products', 'lots.product_id', '=', 'products.id')
            ->select('lot_inquiries.*', 'products.name as product_name', 'products.id as product_id','lots.number as lot_number')
            ->latest()
            ->paginate(15);

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
    public function export()
    {
        $fileName = 'lot-inquiries-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new LotInquiriesExport, $fileName);
    }
}
