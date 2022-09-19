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
                'name' => 'FOOD-AND-BEVERAGES',
                'sub'=> json_encode([
                    'Hot and Iced Coffee',
                    'Frappuchino',
                    'Fruit Tea',
                    'Milk Tea',
                    'Pastries',
                    'Pasta',
                    'Sandwiches',
                    'Waffle',
                    'All Day Breakfast'
                ]),
                'from' => 'kitchen'
            ],
            [
                'name' => 'COFFEE-ESSENTIALS',
                'sub'=> json_encode([
                    'Honey',
                    'Sugar',
                    'Cacao',
                    'Torani Syrup',
                    'Torani Sauce',
                    'Davinci Syrup',
                    'Davinci Sauce'
                ]),
                'from' => 'storage'
            ],
            [
                'name' => 'COFFEE-EQUIPMENT',
                'from' => 'storage',
                'sub'=> json_encode([])
            ],
            [
                'name' => 'ROASTED-BEANS',
                'sub'=> json_encode([
                    'Classic',
                    'Flavoured Aroma',
                    'Philippine Single Origin',
                    'International Single Origin'
                ]),
                'from' => 'storage'
            ],
            [
                'name' => 'GREEN-BEANS',
                'sub'=> json_encode([
                    'Classic',
                    'Philippine Single Origin',
                    'International Single Origin'
                ]),
                'from' => 'storage'
            ],
        ];
        DB::table('menu_categories')->insert($data);
        $this->command->info('Seeder completed successfully');
    }
}
