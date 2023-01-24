<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuAddOnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_add_ons', function (Blueprint $table) {
            $table->id();
            $table->string('menu_id');
            $table->string('inventory_id')->nullable();
            $table->string('qty');
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
        Schema::dropIfExists('menu_add_ons');
    }
}
