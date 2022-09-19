<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id');
            $table->string('menu_id');
            $table->string('inventory_id')->nullable();
            $table->json('data');
            $table->string('name');
            $table->string('type');
            $table->integer('units');
            $table->decimal('price', 8, 2);
            $table->string('qty');
            $table->decimal('total', 8, 2);
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
        Schema::dropIfExists('carts');
    }
}
