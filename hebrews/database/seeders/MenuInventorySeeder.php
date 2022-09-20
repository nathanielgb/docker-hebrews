<?php

namespace Database\Seeders;
use App\Models\MenuInventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuInventory::truncate();

        $data = [
            // Green Beans
            [
                'name' => 'Green Beans - Benguet Arabica(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Sagada Arabica(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Cavite Robusta(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Kalinga Robusta(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Batangas Barako(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Excelsa(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Mt.Apo(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Atok Benguet(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Sultan Kudarat Daguma(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Mt.Matutum(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Arabica Decaf(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Robusta Decaf(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Liberica Premium(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Civet Alamid Musang(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Ethiopia Sidamo(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Ethiopia Yirgacheffe(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Guatemala(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Brazil Santos(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Green Beans - Columbia Supremo(1Kg)',
                'unit' => 'Kg',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],

            //Pastries
            [
                'name' => 'Pastries - Cookies',
                'unit' => 'pcs',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Pastries - Cheesecake',
                'unit' => 'pcs',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Pastries - Garlic Cheese Bread',
                'unit' => 'pcs',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
            [
                'name' => 'Pastries - Muffins',
                'unit' => 'pcs',
                'stock' => '1000',
                'previous_stock' => '0',
                'modified_by' => 'system',
            ],
        ];
        DB::table('menu_inventories')->insert($data);
        $this->command->info('Seeder completed successfully');
    }
}
