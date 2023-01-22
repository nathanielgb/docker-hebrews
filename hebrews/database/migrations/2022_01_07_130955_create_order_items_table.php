<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_item_id')->unique();
            $table->string('order_id');
            $table->string('menu_id');
            $table->string('inventory_id')->nullable();
            $table->string('inventory_code')->nullable();
            $table->string('inventory_name')->nullable();
            $table->string('name');
            $table->string('from');
            $table->string('type');
            $table->decimal('price', 8, 2);
            $table->string('unit_label')->nullable();
            $table->decimal('units', 8, 3);
            $table->integer('qty');
            $table->json('data');
            $table->decimal('total_amount', 8, 2);
            $table->string('status')->default('preparing');
            $table->boolean('kitchen_cleared')->default(false);
            $table->boolean('dispatcher_cleared')->default(false);
            $table->boolean('production_cleared')->default(false);
            $table->string('served_by')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
