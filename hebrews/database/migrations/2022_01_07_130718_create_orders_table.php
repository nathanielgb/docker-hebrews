<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('branch_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('server_name')->nullable();
            $table->json('table')->nullable();
            $table->decimal('subtotal', 8, 2);
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->decimal('fees', 8, 2);
            $table->decimal('total_amount', 8, 2);
            $table->decimal('deposit_bal', 8, 2)->default(0);
            $table->decimal('remaining_bal', 8, 2)->default(0);
            $table->decimal('confirmed_amount', 8, 2)->default(0);
            $table->decimal('amount_given', 8, 2)->default(0);
            $table->string('order_type')->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount_unit', 8, 2);
            $table->string('delivery_method')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->boolean('completed')->default(false);
            $table->boolean('pending')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->boolean('paid')->default(false);
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->string('bank_id')->nullable();
            $table->string('credited_by')->nullable();
            $table->string('confirmed_by')->nullable();
            $table->text('cancelled_by')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
