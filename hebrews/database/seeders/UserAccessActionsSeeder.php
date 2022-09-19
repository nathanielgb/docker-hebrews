<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\AccessAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAccessActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccessAction::truncate();

        $data = [
            [
                'name' => 'SUPERADMIN',
                'access' => json_encode([
                    // User
                    'view-users-action',
                    'manage-user-action',
                    'manage-manager-action',

                    // Customer
                    'view-customers-action',
                    'manage-customer-action',

                    // Bank Accounts
                    'view-bank-accounts-action',
                    'manage-bank-account-action',

                    //Menu
                    'view-menus-action',
                    'manage-menu-action',

                    'view-menu-addons-action',
                    'manage-menu-addons-action',

                    // Categories
                    'view-categories-action',
                    'manage-categories-action',

                    // Inventory
                    'view-inventory-action',
                    'manage-inventory-action',

                    // Discounts
                    'view-discounts-action',
                    'manage-discounts-action',

                    // Orders
                    'take-orders-action',
                    'view-order-list-action',
                    'add-order-item-action',
                    'manage-order-item-action',
                    'manage-orders-action',
                    'generate-order-report-action',

                    // Cook
                    'view-cook-dashboard-action',
                    'manage-cook-actions',

                    // Bar
                    'view-bar-dashboard-action',
                    'manage-bar-actions',

                    // Dispatcher
                    'view-dispatch-dashboard-action',
                    'manage-dispatch-actions',

                    // Manager
                    'manage-order-process-action',
                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
            [
                'name' => 'ADMIN',
                'access' => json_encode([
                    // User
                    'view-users-action',
                    'manage-user-action',
                    'manage-manager-action',

                    // Customer
                    'view-customers-action',
                    'manage-customer-action',

                    // Bank Accounts
                    'view-bank-accounts-action',
                    'manage-bank-account-action',

                    //Menu
                    'view-menus-action',
                    'manage-menu-action',

                    'view-menu-addons-action',
                    'manage-menu-addons-action',

                    // Categories
                    // 'view-categories-action',
                    // 'manage-categories-action',

                    // Inventory
                    'view-inventory-action',
                    'manage-inventory-action',

                    // Discounts
                    'view-discounts-action',
                    'manage-discounts-action',

                    // Orders
                    'take-orders-action',
                    'view-order-list-action',
                    'add-order-item-action',
                    'manage-order-item-action',
                    'manage-orders-action',
                    'generate-order-report-action',

                    // Cook
                    'view-cook-dashboard-action',
                    'manage-cook-actions',

                    // Bar
                    'view-bar-dashboard-action',
                    'manage-bar-actions',

                    // Dispatcher
                    'view-dispatch-dashboard-action',
                    'manage-dispatch-actions',

                    // Manager
                    'manage-order-process-action',
                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
            [
                'name' => 'MANAGER',
                'access' => json_encode([
                    // User
                    'view-users-action',
                    'manage-user-action',

                    //Menu
                    'view-menus-action',
                    'manage-menu-action',

                    // Inventory
                    'view-inventory-action',
                    'manage-inventory-action',

                    // Orders
                    'view-order-list-action',
                    'add-order-item-action',
                    'manage-order-item-action',
                    'manage-orders-action',

                    // Manager
                    'manage-order-process-action',
                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
            [
                'name' => 'WAITER',
                'access' => json_encode([
                    //Menu
                    'view-menus-action',

                    // Inventory
                    'view-inventory-action',

                    // Orders
                    'take-orders-action',
                    'view-order-list-action',
                    'add-order-item-action',
                    'manage-order-item-action',

                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
            [
                'name' => 'KITCHEN',
                'access' => json_encode([

                    //Menu
                    'view-menus-action',
                    'manage-menu-action',

                    // Inventory
                    'view-inventory-action',
                    'manage-inventory-action',

                    // Orders
                    'view-order-list-action',

                    // Cook
                    'view-cook-dashboard-action',
                    'manage-cook-actions',
                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
            [
                'name' => 'BAR',
                'access' => json_encode([

                    //Menu
                    'view-menus-action',
                    'manage-menu-action',

                    // Inventory
                    'view-inventory-action',
                    'manage-inventory-action',

                    // Orders
                    'view-order-list-action',

                    // Bar
                    'view-bar-dashboard-action',
                    'manage-bar-actions',
                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
            [
                'name' => 'DISPATCHER',
                'access' => json_encode([
                    //Menu
                    'view-menus-action',

                    // Inventory
                    'view-inventory-action',

                    // Orders
                    'view-order-list-action',

                    // Dispatcher
                    'view-dispatch-dashboard-action',
                    'manage-dispatch-actions',
                ]),
                'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
            ],
        ];
        DB::table('access_actions')->insert($data);
        $this->command->info('Seeder completed successfully');
    }
}
