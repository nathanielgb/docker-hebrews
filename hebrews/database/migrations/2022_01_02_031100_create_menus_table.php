<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('branch_id')->nullable();
            $table->decimal('reg_price', 8, 2)->nullable();
            $table->decimal('retail_price', 8, 2)->nullable();
            $table->decimal('wholesale_price', 8, 2)->nullable();
            $table->decimal('distributor_price', 8, 2)->nullable();
            $table->decimal('rebranding_price', 8, 2)->nullable();
            $table->decimal('units', 8, 3);
            $table->string('category_id');
            $table->string('inventory_id')->nullable();
            $table->string('sub_category')->nullable();
            $table->boolean('is_beans')->default(false);
            $table->json('data')->nullable();

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
        Schema::dropIfExists('menus');
    }
}
