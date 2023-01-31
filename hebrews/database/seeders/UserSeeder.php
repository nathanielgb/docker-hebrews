<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::truncate();

        $data = [
            [
                'name' => 'SUPERADMIN',
                'email' => 'super@admin.com',
                'username' => 'superadmin',
                'branch_id' => null,
                'type' => 'SUPERADMIN',
                'password' => Hash::make('admin123')
            ],
            [
                'name' => 'ADMIN',
                'email' => 'admin@admin.com',
                'username' => 'admin',
                'branch_id' => null,
                'type' => 'ADMIN',
                'password' => Hash::make('admin123')
            ],
            // [
            //     'name' => 'SAMPLE-MANAGER',
            //     'email' => 'manager@admin.com',
            //     'username' => 'manager',
            //     'branch_id' => '1',
            //     'type' => 'MANAGER',
            //     'password' => Hash::make('admin123')
            // ],
            // [
            //     'name' => 'SAMPLE-KITCHEN',
            //     'email' => 'kitchen@admin.com',
            //     'username' => 'kitchen',
            //     'branch_id' => '1',
            //     'type' => 'KITCHEN',
            //     'password' => Hash::make('admin123')
            // ],
            // [
            //     'name' => 'SAMPLE-WAITER',
            //     'email' => 'waiter@admin.com',
            //     'username' => 'waiter',
            //     'branch_id' => '1',
            //     'type' => 'WAITER',
            //     'password' => Hash::make('admin123')
            // ],
            // [
            //     'name' => 'SAMPLE-DISPATCHER',
            //     'email' => 'dispatcher@admin.com',
            //     'username' => 'dispatcher',
            //     'branch_id' => '1',
            //     'type' => 'DISPATCHER',
            //     'password' => Hash::make('admin123')
            // ],
            // [
            //     'name' => 'SAMPLE-PRODUCTION',
            //     'email' => 'production@admin.com',
            //     'username' => 'production',
            //     'branch_id' => '1',
            //     'type' => 'PRODUCTION',
            //     'password' => Hash::make('admin123')
            // ],
            // [
            //     'name' => 'SAMPLE-TEAMLEADER',
            //     'email' => 'teamleader@admin.com',
            //     'username' => 'teamleader',
            //     'branch_id' => '1',
            //     'type' => 'TEAMLEADER',
            //     'password' => Hash::make('admin123')
            // ],
        ];
        DB::table('users')->insert($data);
        $this->command->info('Seeder completed successfully');

    }
}
