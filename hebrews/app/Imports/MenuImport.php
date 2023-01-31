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
        $this->records = [];

        foreach ($rows as $row) {
            $row['row_number'] = ++$rowNum;
            $status = 'success';

            // make code lowercase
            $row['inventory_code'] =  empty(!$row['inventory_code']) ? strtolower(str_replace(' ', '',  $row['inventory_code'])) : null;
            $action = strtoupper($row['action']);

            switch ($action) {
                case 'A':
                    $exist = Menu::where('code', $row['code'])->where('branch_id', $row['branch_id'])->exists();

                    // Add only if item does not exist
                    if (!$exist) {
                        $record = [
                            'row_number' => $rowNum,
                            'name' => $row['name'] ?? null,
                            'code' => $row['code'] ?? null,
                            'category' => $row['category'] ?? null,
                            'sub_category' => $row['sub_category'] ?? null,
                            'branch_id' => $row['branch_id'] ?? null,
                            'inventory_code' => $row['inventory_code'],
                            'units' => $row['units'] ?? null,
                            'regular_price' => $row['regular_price'] ?? null,
                            'retail_price' => $row['retail_price'] ?? null,
                            'wholesale_price' => $row['wholesale_price'] ?? null,
                            'distributor_price' => $row['distributor_price'] ?? null,
                            'rebranding_price' => $row['rebranding_price'] ?? null,
                            'is_beans' => $row['is_beans'] ?? false,
                            'action' => 'Add',
                            'error' => []
                        ];
                        $validate = $this->validateAddRow($record);
                        if (!empty($validate['errors'])) {
                            $record['status'] = 'failed';
                            foreach($validate['errors'] as $column => $error) {
                                $record['errors'][$column] = $error;
                            }
                        } else {
                            $record['status'] = 'success';

                            $inventory = BranchMenuInventory::where('branch_id', '=', $row['branch_id'])->where('inventory_code', '=', $row['inventory_code'] ?? null)->first();
                            $category = MenuCategory::where('name', $row['category'])->first();

                            $menu = Menu::create([
                                'branch_id' => $row['branch_id'],
                                'code' => $row['code'],
                                'name' => $row['name'],
                                'reg_price' => $row['regular_price'],
                                'retail_price' => $row['retail_price'],
                                'wholesale_price' => $row['wholesale_price'],
                                'distributor_price' => $row['distributor_price'],
                                'rebranding_price' => $row['rebranding_price'],
                                'units' => $row['units'],
                                'category_id' => $category->id ?? null,
                                'sub_category' => $row['sub_category'],
                                'inventory_id' => isset($inventory) ? (string) $inventory->id : null,
                                'is_beans' => $row['is_beans'] ? true : false,
                            ]);

                            $record['menu_id'] = $menu->id;
                        }

                        $records[] = $record;
                    }
                    break;
                case 'U':
                    $record = [
                        'row_number' => $rowNum,
                        'name' => $row['name'] ?? null,
                        'code' => $row['code'] ?? null,
                        'category' => $row['category'] ?? null,
                        'sub_category' => $row['sub_category'] ?? null,
                        'branch_id' => $row['branch_id'] ?? null,
                        'inventory_code' => $row['inventory_code'],
                        'units' => $row['units'] ?? null,
                        'regular_price' => $row['regular_price'] ?? null,
                        'retail_price' => $row['retail_price'] ?? null,
                        'wholesale_price' => $row['wholesale_price'] ?? null,
                        'distributor_price' => $row['distributor_price'] ?? null,
                        'rebranding_price' => $row['rebranding_price'] ?? null,
                        'is_beans' => $row['is_beans'] ?? false,
                        'action' => 'Update',
                        'error' => []
                    ];

                    $item = Menu::where('code', $row['code'])->where('branch_id', $row['branch_id'])->first();

                    if (!$item) {
                        $record['status'] = 'failed';
                        $record['errors']['others'][] = 'Item does not exist.';
                        $records[] = $record;
                        break;
                    } else {
                        $validate = $this->validateUpdateRow($record, $item->id);

                        if (!empty($validate['errors'])) {
                            $record['status'] = 'failed';
                            foreach($validate['errors'] as $column => $error) {
                                $record['errors'][$column] = $error;
                            }

                            $records[] = $record;
                            break;
                        } else {
                            $record['status'] = 'success';

                            // Get Category
                            $category = MenuCategory::where('name', $row['category'])->first();
                            $inventory = BranchMenuInventory::where('branch_id', '=', $row['branch_id'])->where('inventory_code', '=', $row['inventory_code'] ?? null)->first();
                            $item->branch_id =  $row['branch_id'];
                            $item->name =  $row['name'];
                            $item->category_id = (string) $category->id;
                            $item->sub_category =  $row['sub_category'] ?? null;
                            $item->inventory_id = isset($inventory) ? (string) $inventory->id : null;
                            $item->units =   number_format($row['units'], 3,  '.', '');
                            $item->reg_price =  number_format($row['regular_price'], 2,  '.', '');
                            $item->retail_price = number_format($row['retail_price'], 2,  '.', '');
                            $item->wholesale_price =  number_format($row['wholesale_price'], 2,  '.', '');
                            $item->distributor_price =  number_format($row['distributor_price'], 2,  '.', '');
                            $item->rebranding_price = number_format($row['rebranding_price'], 2,  '.', '');
                            $item->is_beans = $row['is_beans'] ? 1 : 0;

                            if ($item->isDirty()) {
                                $item->save();
                                // changes have been made
                                $records[] = $record;
                                break;
                            }
                        }
                    }
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
        $data = array_filter($data);
        // dd($data);
        $validator = Validator::make($data, [
            'branch_id' => ['required', Rule::exists('branches', 'id')],
            'inventory_code' =>  ['sometimes', 'required',Rule::exists('branch_menu_inventories')->where(function ($query) use ($data) {
                $query->where('branch_id', '=', $data['branch_id']);
                $query->where('inventory_code', '=', $data['inventory_code']);
            })],
            'category' => ['required', Rule::exists('menu_categories', 'name')->where(function ($query) use ($data) {
                $query->where('name', '=', $data['category']);
                // $query->whereJsonContains('sub', $data['sub_category']);
            })],
            // 'sub_category' => ['sometimes', 'required', Rule::exists('menu_categories', 'name')->where(function ($query) use ($data) {
            //     $query->where('name', '=', $data['category']);
            //     $query->whereJsonContains('sub', $data['sub_category']);
            // })],
            'code' => ['required', 'max:255', 'alpha_dash', Rule::unique('menus')->where(function ($query) use ($data) {
                $query->where('branch_id', $data['branch_id']);
            })],
            'name' => ['required', 'max:255', Rule::unique('menus')->where(function ($query) use ($data) {
                $query->where('branch_id', $data['branch_id']);
            })],
            'units' => ['required', 'numeric', 'gt:0'],
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
    private function validateUpdateRow($data, $menu_id)
    {
        $data = array_filter($data);

        $validator = Validator::make($data, [
            'branch_id' => ['required', Rule::exists('branches', 'id')],
            'inventory_code' =>  ['sometimes', 'required',Rule::exists('branch_menu_inventories')->where(function ($query) use ($data) {
                $query->where('branch_id', '=', $data['branch_id']);
                $query->where('inventory_code', '=', $data['inventory_code']);
            })],
            'category' => ['required', Rule::exists('menu_categories', 'name')->where(function ($query) use ($data) {
                $query->where('name', '=', $data['category']);
                // $query->whereJsonContains('sub', $data['sub_category']);
            })],
            // 'sub_category' => ['sometimes', 'required', Rule::exists('menu_categories', 'name')->where(function ($query) use ($data) {
            //     $query->where('name', '=', $data['category']);
            //     $query->whereJsonContains('sub', $data['sub_category']);
            // })],
            'name' => ['required', 'max:255', Rule::unique('menus', 'name')->where(function ($query) use ($data) {
                $query->where('branch_id', $data['branch_id']);
            })->ignore($menu_id)],
            'code' => ['required', 'max:255', 'alpha_dash', Rule::unique('menus', 'code')->where(function ($query) use ($data) {
                $query->where('branch_id', $data['branch_id']);
            })->ignore($menu_id)],
            'units' => ['required', 'numeric', 'gt:0'],
            'regular_price' => 'nullable|numeric|between:0,999999.99',
            'retail_price' => 'nullable|numeric|between:0,999999.99',
            'wholesale_price' => 'nullable|numeric|between:0,999999.99',
            'distributor_price' => 'nullable|numeric|between:0,999999.99',
            'rebranding_price' => 'nullable|numeric|between:0,999999.99',
        ]);


        return [
            'errors' => $validator->errors()->messages()
        ];
    }
}
