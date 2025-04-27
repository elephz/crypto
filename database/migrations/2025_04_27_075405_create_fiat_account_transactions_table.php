<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiatAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fiat_account_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('type', ['topup', 'withdrawal'])->default('topup');
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
            $table->unsignedBigInteger('fiat_account_id');
            $table->foreign('fiat_account_id')->references('id')->on('fiat_accounts');
            $table->decimal('amount', 30, 16);
            $table->decimal('fee', 30, 16)->default(0);
            $table->decimal('balance', 30, 16)->default(0);
            $table->string('description')->nullable();
            $table->string('transaction_number')->unique();
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
        Schema::dropIfExists('fiat_account_transactions');
    }
}
