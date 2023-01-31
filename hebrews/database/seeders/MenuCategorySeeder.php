<?php

namespace Database\Seeders;
use App\Models\MenuCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuCategory::truncate();

        $data = [
            [
                'name' => 'Iced and Hot Coffee',
                'sub'=> json_encode([
                    'Espresso Based - Hot',
                    'Espresso Based - Iced'
                ]),
                'from' => 'kitchen'
            ],
            [
                'name' => 'Frappuchino',
                'sub'=> json_encode([
                    'Espresso Based - Frappe',
                    'Non coffee - Frappe'
                ]),
                'from' => 'kitchen'
            ],
            [
                'name' => 'Fruit Tea',
                'sub'=> json_encode([
                    'Hot Tea',
                    'Cold Tea'
                ]),
                'from' => 'kitchen'
            ],
            [
                'name' => 'Fruit Juice',
                'sub'=> json_encode([
                    'Lemon Juice',
                    'Orange Juice',
                    'Dalandan Juice',
                    'Calamansi Juice',
                ]),
                'from' => 'kitchen'
            ],
            [
                'name' => 'Food',
                'sub'=> json_encode([
                    'Pastries',
                    'Filipino Deli',
                    'Pasta',
                    'Sandwiches',
                    'Waffles',
                    'All day Breakfast',
                    'Ala Carte',
                ]),
                'from' => 'kitchen'
            ],
            [
                'name' => 'Native Flavouring',
                'sub'=> json_encode([
                    'Sweetener',
                    'Native Chocolate',
                ]),
                'from' => 'storage'
            ],
            [
                'name' => 'Flavoring Syrups and Sauces',
                'sub'=> json_encode([
                    'Torani Syrup',
                    'Torani Sauces',
                    'DaVinci Gourmet Classic Syrup'
                ]),
                'from' => 'storage'
            ],
            [
                'name' => 'Flavouring Milk and Creamer',
                'sub'=> json_encode([
                ]),
                'from' => 'storage'
            ],
            [
                'name' => 'Coffee Essentials',
                'sub'=> json_encode([
                    'Coffee Equipment',
                    'Consumables'
                ]),
                'from' => 'storage'
            ],
            [
                'name' => 'Roasted Beans',
                'sub'=> json_encode([
                    'Classic Coffee Beans',
                    'Flavored Coffee Beans',
                    'Single Origin Coffee Beans',
                    'International Coffee Beans'
                ]),
                'from' => 'storage'
            ],
        ];
        DB::table('menu_categories')->insert($data);
        $this->command->info('Seeder completed successfully');
    }
}
