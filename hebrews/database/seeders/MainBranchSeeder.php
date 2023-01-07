<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data = [
            [
                'id'=> 1,
                'name' => 'Main Branch',
                'location' => ''
            ],
            [
                'id'=> 2,
                'name' => 'Sub Branch',
                'location' => ''
            ]
        ];
        DB::table('branches')->insert($data);
        $this->command->info('Seeder completed successfully');
    }
}
