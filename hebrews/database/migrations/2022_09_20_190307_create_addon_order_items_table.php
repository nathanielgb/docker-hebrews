<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('order_item_id');
            $table->string('addon_id');
            $table->string('inventory_id')->nullable();
            $table->string('inventory_code')->nullable();
            $table->string('inventory_name')->nullable();
            $table->string('menu_id')->nullable();
            $table->string('unit_label')->default('pcs');
            $table->decimal('unit', 8, 2)->default(1);
            $table->integer('qty');
            $table->boolean('is_dinein')->default(false);
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
        Schema::dropIfExists('addon_order_items');
    }
}
