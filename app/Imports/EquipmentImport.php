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

    // public function model(array $row)
    // {
    //     // dd($row);
    //     $facility = Facility::where('name', $row['facility_id'])->first();
    //     $category = Category::where('name', $row['category_id'])->first();
    //     $stock_unit = StockUnit::where('description', $row['stock_unit'])->first();


    //     $data = [
    //         'unit_no' => $row['unit_no'],
    //         'description' => $row['description'],
    //         'specifications' => $row['specifications'],
    //         'facility_id' => $facility->id,
    //         'category_id' => $category->id,
    //         'stock_unit' =>  $stock_unit->id,
    //         'status' => $row['status'],
    //         'date_acquired' => $row['date_acquired'],
    //         'supplier' => $row['supplier'],
    //         'amount' => $row['amount'],
    //         'estimated_life' => $row['estimated_life'],
    //         'item_no' => $row['item_no'],
    //         'property_no' => $row['property_no'],
    //         'control_no' => $row['control_no'],
    //         'serial_no' => $row['serial_no'],
    //         'no_of_stocks' => $row['no_of_stocks'],
    //         'restocking_point' => $row['restocking_point'],
    //         'person_liable' => $row['person_liable'],
    //         'remarks' => $row['remarks'],
    //     ];


    //     $equipment = new Equipment($data);

    //     return $equipment;

    //     // dd($equipment);
    // }

    public function model(array $row)
    {
        // Retrieve related models or set to null if not found
        $facility = Facility::where('name', $row['facility_id'] ?? '')->first();
        $category = Category::where('description', $row['category_id'] ?? '')->first();
        $stock_unit = StockUnit::where('description', $row['stock_unit_id'] ?? '')->first();
    
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