<?php

namespace App\Imports;

use App\Models\Equipment;
use App\Models\Facility;
use App\Models\Category;
use App\Models\StockUnit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\Log;


class EquipmentImport implements ToModel, WithHeadingRow
{



    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

 

    //     // dd($equipment);
    // }

    public function model(array $row)
    {
        $userId = auth()->id(); 
       

        $facilityName = trim($row['facility_id'] ?? '');
        $categoryDescription = trim($row['category_id'] ?? '');
        $stockUnitDescription = trim($row['stock_unit_id'] ?? '');

        $facility = $facilityName ? Facility::firstOrCreate(['name' => $facilityName], ['name' => $facilityName]) : null;
        $category = $categoryDescription ? Category::firstOrCreate(['description' => $categoryDescription], ['description' => $categoryDescription]) : null;
        $stock_unit = $stockUnitDescription ? StockUnit::firstOrCreate(['description' => $stockUnitDescription], ['description' => $stockUnitDescription]) : null;

    
        // Prepare data array with null checks
        $data = [
            'unit_no' => $row['unit_no'] ?? null,
            'description' => $row['description'] ?? null,
            'specifications' => $row['specifications'] ?? null,
            'facility_id' => $facility ? $facility->id : null,
            'category_id' => $category ? $category->id : null,
            'status' => $row['status'] ?? null,
            'date_acquired' => $row['date_acquired'] ?? null,
            'supplier' => $row['supplier'] ?? null,
            'amount' => $row['amount'] ?? null,
            'estimated_life' => $row['estimated_life'] ?? null,
            'item_no' => $row['item_no'] ?? null,
            'property_no' => $row['property_no'] ?? null,
            'control_no' => $row['control_no'] ?? null,
            'serial_no' => $row['serial_no'] ?? null,
            'no_of_stocks' => $row['no_of_stocks'] ?? null,
            'restocking_point' => $row['restocking_point'] ?? null,
            'stock_unit_id' => $stock_unit ? $stock_unit->id : null,
            'person_liable' => $row['person_liable'] ?? null,
            'user_id' => $userId ?? null, 
            'remarks' => $row['remarks'] ?? null,
        ];
    
        // Check if the row has any meaningful data before inserting
        if (!array_filter($data, fn($value) => !is_null($value) && $value !== '')) {
            // If the row is blank, return null to skip insertion
            return null;
        }
    
        // Create and return new Equipment instance if the row has data
        return new Equipment($data);
    }
}    