<?php

namespace App\Imports;

use App\Models\MenuInventory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        //
        foreach ($rows as $row) {
            switch ($row['action']) {
                case 'A':
                    if ($row['branch_id'] == 1) {
                        $inventory = MenuInventory::create([
                            'branch_id' => 1,
                            'inventory_code' => $row['inventory_code'],
                            'name' => $row['name'],
                            'unit' => $row['unit'],
                            'stock' => $row['stock'],
                            'previous_stock' => 0,
                            'modified_by' => 'SYSTEM'
                        ]);
                    } else {


                    }
                    break;
                case 'U':
                    # code...
                    break;

                default:
                    break;
            }
        }
    }
}
