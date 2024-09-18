<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;


class CategoryImport implements ToModel, WithHeadingRow
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

     public function model(array $row)
    {
        $userId = auth()->id(); 

        $categoryDescription = trim($row['category_id'] ?? '');
        $category = $categoryDescription ? Category::firstOrCreate(['description' => $categoryDescription], ['description' => $categoryDescription]) : null;
 // Prepare data array with null checks
 $data = [
    'description' => $row['description'] ?? null,
   
    
];
        // Check if the row has any meaningful data before inserting
        if (!array_filter($data, fn($value) => !is_null($value) && $value !== '')) {
            // If the row is blank, return null to skip insertion
            return null;
        }
         // Create and return new Equipment instance if the row has data
         return new Category($data);
        }
    }    



  