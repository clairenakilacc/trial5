<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;


class FacilityImport implements ToModel, WithHeadingRow
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

        $facilityName = trim($row['facility_id'] ?? '');
        $facility = $facilityName ? Facility::firstOrCreate(['name' => $facilityName], ['name' => $facilityName]) : null;
 // Prepare data array with null checks
 $data = [
    'name' => $row['name'] ?? null,
    'connection_type' => $row['connection_type'] ?? null,
    'facility_type' => $row['facility_type'] ?? null,
    'cooling_tools' => $row['cooling_tools'] ?? null,
    'floor_level' => $row['floor_level'] ?? null,
    'building' => $row['building'] ?? null,
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



  