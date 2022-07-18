<?php

namespace App\Http\Controllers\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportCategory implements FromCollection
{
    public function collection()
    {
        // return Category::with('name', 'slug')->get();
        return Category::select('name', 'slug')->get();
    }
}
