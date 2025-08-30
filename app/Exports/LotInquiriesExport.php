<?php

namespace App\Exports;

use App\Models\LotInquiry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LotInquiriesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Eager load the relationships to prevent N+1 query problems
        return LotInquiry::with(['lot', 'product'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // These will be the column headers in the Excel file
        return [
            'Inquiry ID',
            'Product Name',
            'Lot Number',
            'Customer Name',
            'Customer Mobile',
            'Customer Address',
            'Date of Inquiry',
        ];
    }

    /**
     * @param LotInquiry $inquiry
     * @return array
     */
    public function map($inquiry): array
    {
        // This maps the data from each $inquiry object to the columns
        return [
            $inquiry->id,
            $inquiry->product->name ?? 'N/A', // Access product name via relationship
            $inquiry->lot->number ?? 'N/A',     // Access lot number via relationship
            $inquiry->name,
            $inquiry->mobile,
            $inquiry->address,
            $inquiry->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
