<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\MenuInventory;
use App\Models\BranchMenuInventory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Validation\Rule;

class InventoryImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
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

            $action = strtoupper($row['action']);

            switch ($action) {
                case 'A':
                    if ($row['branch_id'] == 1) {
                        $exist = MenuInventory::where('inventory_code', $row['inventory_code'])->exists();

                        // Add only if item does not exist
                        if (!$exist) {
                            // $error = ['inventory_code' => 'Inventory item already exist.'];
                            // throw ValidationException::withMessages($error);
                            $record = [
                                'row_number' => $rowNum,
                                'branch_id' => $row['branch_id'],
                                'inventory_code' => $row['inventory_code'],
                                'name' => $row['name'],
                                'unit' => $row['unit'],
                                'stock' => $row['stock'],
                                'action' => 'Add',
                            ];

                            $validate = $this->validateAddRow($row);

                            if (!empty($validate['errors'])) {
                                $record['status'] = 'failed';
                                $record['errors'] = $validate['errors'];

                            } else {
                                $record['status'] = 'success';

                                $inventory = MenuInventory::create([
                                    'branch_id' => 1,
                                    'inventory_code' => $row['inventory_code'],
                                    'name' => $row['name'],
                                    'unit' => $row['unit'],
                                    'stock' => $row['stock'],
                                    'previous_stock' => 0,
                                    'modified_by' => 'SYSTEM'
                                ]);
                            }

                            $records[] = $record;
                        }
                    } else {
                        $exist = BranchMenuInventory::where('branch_id', $row['branch_id'])->where('inventory_code', $row['inventory_code'])->exists();

                        if (!$exist) {
                            $record = [
                                'row_number' => $rowNum,
                                'branch_id' => $row['branch_id'],
                                'inventory_code' => $row['inventory_code'],
                                'name' => $row['name'],
                                'unit' => $row['unit'],
                                'stock' => $row['stock'],
                                'action' => 'Add',
                                'errors' => []
                            ];

                            $validate = $this->validateAddRow($row);
                            if (!empty($validate['errors'])) {
                                $record['status'] = 'failed';
                                foreach($validate['errors'] as $column => $error) {
                                    $record['errors'][$column] = $error;
                                }
                            } else {
                                $record['status'] = 'success';

                                $inventory = BranchMenuInventory::create([
                                    'branch_id' => $row['branch_id'],
                                    'inventory_code' => $row['inventory_code'],
                                    'name' => $row['name'],
                                    'unit' => $row['unit'],
                                    'stock' => $row['stock'],
                                    'previous_stock' => 0,
                                    'modified_by' => 'SYSTEM'
                                ]);
                            }

                            $records[] = $record;
                        }
                    }
                    break;
                case 'U':
                    $record = [
                        'row_number' => $rowNum,
                        'branch_id' => $row['branch_id'],
                        'inventory_code' => $row['inventory_code'],
                        'name' => $row['name'],
                        'unit' => $row['unit'],
                        'stock' => $row['stock'],
                        'action' => 'Update',
                        'errors' => []
                    ];
                    if ($row['branch_id'] == 1) {
                        $item = MenuInventory::where('inventory_code', $row['inventory_code'])->first();

                        if (!$item) {
                            $record['status'] = 'failed';
                            $record['errors']['others'][] = 'Item does not exist.';
                        } else {
                            $validate = $this->validateUpdateRow($row);

                            if (!empty($validate['errors'])) {
                                $record['status'] = 'failed';
                                foreach($validate['errors'] as $column => $error) {
                                    $record['errors'][$column] = $error;
                                }
                            } else {
                                $record['status'] = 'success';

                                $item->previous_stock = $item->stock;
                                $item->stock = $row['stock'];
                                $item->save();
                            }
                        }
                    } else {
                        $item = MenuInventory::where('branch_id', $row['branch_id'])->where('inventory_code', $row['inventory_code'])->first();
                        if (!$item) {
                            $record['status'] = 'failed';
                            $record['errors']['others'][] = 'Item does not exist.';
                        } else {
                            $validate = $this->validateUpdateRow($row);

                            if (!empty($validate['errors'])) {
                                $record['status'] = 'failed';
                                foreach($validate['errors'] as $column => $error) {
                                    $record['errors'][$column] = $error;
                                }
                            } else {
                                $record['status'] = 'success';

                                $item->previous_stock = $item->stock;
                                $item->stock = $row['stock'];
                                $item->save();
                            }
                        }
                    }

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
            'branch_id' => ['required', Rule::exists(Branch::class, 'id')],
            'inventory_code' => 'required|max:255|alpha_dash',
            'name' => 'required|max:255',
            'unit' => ['required', Rule::in(['Kg', 'g', 'pcs', 'boxes'])],
            'stock' => 'required|numeric|between:0,9999999'
        ]);

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