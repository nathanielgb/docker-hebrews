<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::view('about', 'about')->name('about');

    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // User Section
    Route::group(['middleware' => 'CheckAccessActions:view-users-action'], function () {
        Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');

        Route::group(['middleware' => 'CheckAccessActions:manage-user-action'], function () {
            Route::get('add-user', [\App\Http\Controllers\UserController::class, 'viewAdd'])->name('users.view_add');
            Route::post('add-user', [\App\Http\Controllers\UserController::class, 'add'])->name('users.add');
            // Route::get('update-user', [\App\Http\Controllers\UserController::class, 'viewUpdate'])->name('users.view_update');
            // Route::post('update-user', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
            Route::post('delete-user', [\App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
            Route::post('reset-user', [\App\Http\Controllers\UserController::class, 'reset'])->name('users.reset');


        });
    });

    // Customer Section
    Route::group(['middleware' => 'CheckAccessActions:view-customers-action'], function () {
        Route::get('customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');

        Route::group(['middleware' => 'CheckAccessActions:manage-customer-action'], function () {
            Route::get('view/{id}', [\App\Http\Controllers\CustomerController::class, 'view'])->name('customers.view');
            Route::post('add-customer', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
            Route::post('delete-customer', [\App\Http\Controllers\CustomerController::class, 'delete'])->name('customers.delete');
        });
    });

    // Bank Accounts Section
    Route::group(['middleware' => 'CheckAccessActions:view-bank-accounts-action'], function () {
        Route::get('bank-accounts', [\App\Http\Controllers\BankAccountController::class, 'index'])->name('bank.accounts.index');
        Route::get('bank-accounts/{account_id}', [\App\Http\Controllers\BankAccountController::class, 'showTransactions'])->name('bank.account.transactions');

        Route::group(['middleware' => 'CheckAccessActions:manage-bank-account-action'], function () {
            Route::post('add-account', [\App\Http\Controllers\BankAccountController::class, 'store'])->name('bank.account.store');
            Route::post('delete-account', [\App\Http\Controllers\BankAccountController::class, 'delete'])->name('bank.account.delete');
            Route::post('reset-account', [\App\Http\Controllers\BankAccountController::class, 'reset'])->name('bank.account.reset');

        });
    });

    // Menu Section
    Route::group(['middleware' => 'CheckAccessActions:view-menus-action'], function () {
        Route::get('menu', [\App\Http\Controllers\MenuController::class, 'index'])->name('menu.index');

        Route::group(['middleware' => 'CheckAccessActions:manage-menu-action'], function () {
            Route::post('add-menu', [\App\Http\Controllers\MenuController::class, 'store'])->name('menu.store');
            // Route::get('update-menu', [\App\Http\Controllers\MenuController::class, 'viewUpdate'])->name('menu.show_update');
            Route::post('update-menu', [\App\Http\Controllers\MenuController::class, 'update'])->name('menu.update');
            Route::post('delete-menu', [\App\Http\Controllers\MenuController::class, 'delete'])->name('menu.delete');
        });

        Route::group(['middleware' => 'CheckAccessActions:view-inventory-action'], function () {
            Route::get('inventory', [\App\Http\Controllers\MenuController::class, 'viewInventory'])->name('menu.view_inventory');
            Route::post('add-inventory', [\App\Http\Controllers\MenuController::class, 'addInventory'])->name('menu.add_inventory');
            Route::post('update-inventory', [\App\Http\Controllers\MenuController::class, 'updateInventory'])->name('menu.update_inventory');
            Route::post('delete-inventory', [\App\Http\Controllers\MenuController::class, 'deleteInventory'])->name('menu.delete_inventory');
        });
    });

    Route::group(['middleware' => 'CheckAccessActions:view-menu-addons-action'], function () {
        Route::get('menu/add-ons', [\App\Http\Controllers\MenuAddOnController::class, 'index'])->name('menu.addon.index');

        Route::group(['middleware' => 'CheckAccessActions:manage-menu-addons-action'], function () {
            Route::post('menu/add-ons', [\App\Http\Controllers\MenuAddOnController::class, 'store'])->name('menu.addon.store');
        });
    });

    // Categories Section ( SUPERADMIN)
    Route::group(['middleware' => 'CheckAccessActions:view-categories-action'], function () {
        Route::get('categories', [\App\Http\Controllers\MenuController::class, 'viewCategories'])->name('menu.show_categories');

        Route::group(['middleware' => 'CheckAccessActions:manage-categories-action'], function () {
            Route::post('add-category', [\App\Http\Controllers\MenuController::class, 'addCategory'])->name('menu.add_category');
            Route::post('delete-category', [\App\Http\Controllers\MenuController::class, 'deleteCategory'])->name('menu.delete_category');
        });
    });

    // Discount Section
    Route::group(['middleware' => 'CheckAccessActions:view-discounts-action'], function () {
        Route::get('discounts', [\App\Http\Controllers\DiscountController::class, 'index'])->name('discount.index');
        Route::group(['middleware' => 'CheckAccessActions:manage-discounts-action'], function () {
            Route::post('add-discount', [\App\Http\Controllers\DiscountController::class, 'store'])->name('discount.store');
            Route::post('update-discount', [\App\Http\Controllers\DiscountController::class, 'update'])->name('discount.update');
            Route::post('delete-discount', [\App\Http\Controllers\DiscountController::class, 'delete'])->name('discount.delete');
        });
    });

    // Take orders section
    Route::group(['middleware' => 'CheckAccessActions:take-orders-action'], function () {
        Route::get('take-order', [\App\Http\Controllers\OrderController::class, 'showTakeOrder'])->name('order.show_take_order');
        Route::get('add-cart', [\App\Http\Controllers\OrderController::class, 'showAddCart'])->name('order.show_add_cart');
        Route::post('add-cart', [\App\Http\Controllers\OrderController::class, 'addCart'])->name('order.add_cart');
        Route::get('view-cart', [\App\Http\Controllers\OrderController::class, 'viewCart'])->name('order.show_cart');
        Route::post('update-cart', [\App\Http\Controllers\OrderController::class, 'updateCart'])->name('order.update_cart');
        Route::post('delete-cart', [\App\Http\Controllers\OrderController::class, 'deleteCart'])->name('order.delete_cart');
        Route::post('generate-order', [\App\Http\Controllers\OrderController::class, 'generateOrder'])->name('order.generate');
    });

    // View orders section
    Route::group(['middleware' => 'CheckAccessActions:view-order-list-action'], function () {
        Route::get('order-summary/{order_id}', [\App\Http\Controllers\OrderController::class, 'showSummary'])->name('order.show_summary');
        Route::get('order-list', [\App\Http\Controllers\OrderController::class, 'showOrders'])->name('order.list');

        Route::get('order/summary/print', [\App\Http\Controllers\OrderController::class, 'printSummary'])->name('order.summary.print');

        Route::group(['middleware' => 'CheckAccessActions:manage-orders-action'], function () {
            Route::post('delete-order-item', [\App\Http\Controllers\OrderController::class, 'deleteOrderItem'])->name('order.delete_item');
        });

        Route::group(['middleware' => 'CheckAccessActions:add-order-item-action'], function () {
            Route::get('add-order-item', [\App\Http\Controllers\OrderController::class, 'showAddOrderItem'])->name('order.show_add_item');
            Route::post('add-order-item/{order_id}', [\App\Http\Controllers\OrderController::class, 'addOrderItem'])->name('order.add_item');
        });

        Route::group(['middleware' => 'CheckAccessActions:manage-order-item-action'], function () {
            Route::get('edit-order-items/{order_id}', [\App\Http\Controllers\OrderController::class, 'showEditOrderItems'])->name('order.edit_items');
            Route::post('edit-order-item', [\App\Http\Controllers\OrderController::class, 'updateOrderItems'])->name('order.update_item');

        });
    });

    // Kitchen section
    Route::group(['middleware' => 'CheckAccessActions:view-cook-dashboard-action'], function () {
        Route::get('kitchen-orders', [\App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.orders.list');
    });

    Route::group(['middleware' => 'CheckAccessActions:manage-cook-actions'], function () {
        Route::get('prepare-order/{item_id}', [\App\Http\Controllers\KitchenController::class, 'prepare'])->name('kitchen.order.prepare');
        Route::post('cancel-order', [\App\Http\Controllers\KitchenController::class, 'cancel'])->name('kitchen.order.cancel');
        Route::post('done-order', [\App\Http\Controllers\KitchenController::class, 'done'])->name('kitchen.order.done');
        Route::post('complete-order', [\App\Http\Controllers\KitchenController::class, 'complete'])->name('kitchen.order.complete');
    });

    // Dispatch section
    Route::group(['middleware' => 'CheckAccessActions:view-dispatch-dashboard-action'], function () {
        Route::get('dispatch-orders', [\App\Http\Controllers\DispatcherController::class, 'index'])->name('dispatch.list');
    });

    Route::group(['middleware' => 'CheckAccessActions:manage-dispatch-actions'], function () {
        Route::post('serve-orders', [\App\Http\Controllers\DispatcherController::class, 'serve'])->name('dispatch.order.serve');
    });

    // Bar section
    Route::group(['middleware' => 'CheckAccessActions:view-bar-dashboard-action'], function () {
        Route::get('bar-orders', [\App\Http\Controllers\BarController::class, 'index'])->name('bar.orders.list');
    });

    Route::group(['middleware' => 'CheckAccessActions:manage-bar-actions'], function () {
        Route::get('bar/prepare-order/{item_id}', [\App\Http\Controllers\BarController::class, 'prepare'])->name('bar.order.prepare');
        Route::post('bar/done-order', [\App\Http\Controllers\BarController::class, 'done'])->name('bar.order.done');
        Route::post('bar/complete-order', [\App\Http\Controllers\BarController::class, 'complete'])->name('bar.order.complete');
    });

    // Process payment and printing
    Route::group(['middleware' => 'CheckAccessActions:manage-order-process-action'], function () {
        Route::get('pay-order/{order_id}', [\App\Http\Controllers\OrderController::class, 'showPayForm'])->name('order.show_payform');
        Route::post('edit-order/{order_id}', [\App\Http\Controllers\OrderController::class, 'edit'])->name('order.edit');
        Route::post('confirm-order/{order_id}', [\App\Http\Controllers\OrderController::class, 'confirm'])->name('order.confirm');
        Route::post('pay-order/{order_id}', [\App\Http\Controllers\OrderController::class, 'pay'])->name('order.pay');
        Route::post('cancel-order/{order_id}', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('order.cancel');
        Route::post('complete-order/{order_id}', [\App\Http\Controllers\OrderController::class, 'complete'])->name('order.complete');
        Route::get('print/receipt/{order_id}', [\App\Http\Controllers\OrderController::class, 'print'])->name('print.receipt');
    });

    // Generate Order Reports
    Route::group(['middleware' => 'CheckAccessActions:generate-order-report-action'], function () {
        Route::get('orders-report', [\App\Http\Controllers\OrderReportController::class, 'showGenerateReport'])->name('orders.report.show');
        Route::get('generate-report', [\App\Http\Controllers\OrderReportController::class, 'generate'])->name('orders.report.generate');
    });



});
