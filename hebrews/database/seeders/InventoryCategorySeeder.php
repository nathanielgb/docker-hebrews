<?php

namespace Database\Seeders;
use App\Models\InventoryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InventoryCategory::truncate();

        $data = [
            [
                'name' => 'Native Flavouring',
            ],
            [
                'name' => 'Flavoring Syrups and Sauces',
            ],
            [
                'name' => 'Flavouring Milk and Creamer',
            ],
            [
                'name' => 'Coffee Essentials',
            ],
            [
                'name' => 'Beans',
            ],
            [
                'name' => 'Packaging',
            ],
            [
                'name' => 'Disposables',
            ],
            [
                'name' => 'Cups',
            ],
            [
                'name' => 'Condiments',
            ],
        ];
        DB::table('inventory_categories')->insert($data);
        $this->command->info('Seeder completed successfully');
    }
}
