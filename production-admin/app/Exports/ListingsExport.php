<?php

namespace App\Exports;

use App\Models\Listing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ListingsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Listing::with(['user', 'category'])->get()->map(function($listing) {
            return [
                'ID' => $listing->id,
                'Title' => $listing->title,
                'Category' => $listing->category->name ?? '',
                'User' => $listing->user->name ?? '',
                'Price' => $listing->price,
                'Location' => $listing->location,
                'Status' => $listing->status,
                'Views' => $listing->views,
                'Created At' => $listing->created_at,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Category', 'User', 'Price', 'Location', 'Status', 'Views', 'Created At'];
    }
}
