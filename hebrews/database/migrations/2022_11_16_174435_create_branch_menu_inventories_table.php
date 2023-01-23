<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchMenuInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_menu_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('branch_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('inventory_code');
            $table->string('name');
            $table->string('unit');
            $table->decimal('stock', 8, 3)->nullable();
            $table->decimal('previous_stock', 8, 3)->nullable();
            $table->string('modified_by');
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
        Schema::dropIfExists('branch_menu_inventories');
    }
}
