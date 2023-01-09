<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\BranchMenuInventory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Validation\Rule;

class MenuImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;
    public $records = [];


    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $errorBag = [];
        $status = '';
        $records = [];
        $rowNum = 1;
        $validate = [];

        foreach ($rows as $row) {
            $row['row_number'] = ++$rowNum;
            $status = 'success';

            // make code lowercase
            $row['inventory_code'] = strtolower(str_replace(' ', '',  $row['inventory_code']));
            
            // remove spaces for category
            $row['category'] = str_replace(' ', '',  $row['category']);

            $action = strtoupper($row['action']);

            switch ($action) {
                case 'A':
                    $record = [
                        'row_number' => $rowNum,
                        'code' => $row['code'],
                        'name' => $row['name'],
                        'category' => $row['category'],
                        'sub_category' => $row['sub_category'],
                        'branch_id' => $row['branch_id'],
                        'inventory_code' => $row['inventory_code'],
                        'units' => $row['units'],
                        'regular_price' => $row['regular_price'],
                        'retail_price' => $row['retail_price'],
                        'wholesale_price' => $row['wholesale_price'],
                        'distributor_price' => $row['distributor_price'],
                        'rebranding_price' => $row['rebranding_price'],
                        'action' => 'Add',
                        'error' => []
                    ];

                    $validate = $this->validateAddRow($row);

                    if (!empty($validate['errors'])) {
                        $record['status'] = 'failed';
                        foreach($validate['errors'] as $column => $error) {
                            $record['errors'][$column] = $error;
                        }
                    } else {
                        $record['status'] = 'success';
                        
                        $inventory = BranchMenuInventory::where('branch_id', '=', $row['branch_id'])->where('inventory_code', '=', $row['inventory_code'])->first();
                        $category = MenuCategory::where('name', $row['category'])->first();

                        $menu = Menu::create([
                            'code' => $row['code'],
                            'name' => $row['name'],
                            'reg_price' => $row['regular_price'],
                            'retail_price' => $row['retail_price'],
                            'wholesale_price' => $row['wholesale_price'],
                            'distributor_price' => $row['distributor_price'],
                            'rebranding_price' => $row['rebranding_price'],
                            'units' => $row['units'],
                            'category_id' => $category->id,
                            'sub_category' => $row['sub_category'],
                            'inventory_id' => $inventory->id,
                        ]);

                        $record['menu_id'] = $menu->id;
                    }

                    $records[] = $record;
                    break;
                case 'U':


                    $records[] = $record;
                    break;
                default:
                    break;
            }
        }

        $this->records = $records;
    }


    /**
    * validate columns of the record and return status/error if any
    *
    * @param array $data
    *
    * @return array
    */
    private function validateAddRow($data)
    {
        $data = $data->toArray();

        $validator = Validator::make($data, [
            'inventory_code' =>  ['required',Rule::exists('branch_menu_inventories')->where(function ($query) use ($data) {
                $query->where('branch_id', '=', $data['branch_id']);
                $query->where('inventory_code', '=', $data['inventory_code']);
            })],
            'category' => ['required', Rule::exists('menu_categories', 'name')->where(function ($query) use ($data) {
                $query->where('name', '=', $data['category']);
                $query->whereJsonContains('sub', $data['sub_category']);
            })],
            'code' => ['required', 'max:255', 'alpha_dash', Rule::unique('menus')],
            'name' => ['required', 'max:255', Rule::unique('menus')],
            'units' => ['required', 'numeric', 'between:1,9999999'],
            'regular_price' => 'nullable|numeric|between:0,999999.99',
            'retail_price' => 'nullable|numeric|between:0,999999.99',
            'wholesale_price' => 'nullable|numeric|between:0,999999.99',
            'distributor_price' => 'nullable|numeric|between:0,999999.99',
            'rebranding_price' => 'nullable|numeric|between:0,999999.99',
        ]);


        // Validate if category exist
        $errors = $validator->errors()->messages();
        
        return [
            'errors' => $errors
        ];
    }

    /**
    * validate columns of the record and return status/error if any
    *
    * @param array $data
    *
    * @return array
    */
    private function validateUpdateRow($data)
    {
        $data = $data->toArray();

        $validator = Validator::make($data, [
            'stock' => 'required|numeric|between:0,9999999'
        ]);
        return [
            'errors' => $validator->errors()->messages()
        ];
    }
}
