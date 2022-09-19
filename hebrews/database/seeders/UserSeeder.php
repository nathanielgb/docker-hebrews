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
                'branch' => 'BRANCH1',
                'type' => 'SUPERADMIN',
				'password' => Hash::make('admin123')
            ],
            [
                'name' => 'ADMIN',
                'email' => 'admin@admin.com',
                'username' => 'admin',
                'branch' => 'BRANCH1',
                'type' => 'ADMIN',
				'password' => Hash::make('admin123')
            ],
        ];
        DB::table('users')->insert($data);
        $this->command->info('Seeder completed successfully');

    }
}
