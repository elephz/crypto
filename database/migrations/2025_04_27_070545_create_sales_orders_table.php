<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string("order_number")->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('fiat_account_id');
            $table->foreign('fiat_account_id')->references('id')->on('fiat_accounts');
            $table->decimal('amount', 30, 16);
            $table->decimal('price', 30, 16);
            $table->decimal('rate', 20, 8);
            $table->unsignedBigInteger('exchange_rate_id');
            $table->foreign('exchange_rate_id')->references('id')->on('exchange_rate');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->softDeletes();
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
        Schema::dropIfExists('sales_orders');
    }
}
