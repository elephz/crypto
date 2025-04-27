<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTranfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_tranfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_wallet_id');
            $table->foreign('from_wallet_id')->references('id')->on('wallets');
            $table->unsignedBigInteger('to_wallet_id')->nullable();
            $table->foreign('to_wallet_id')->references('id')->on('wallets');
            $table->decimal('amount', 30, 16);
            $table->decimal('fee', 30, 16);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
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
        Schema::dropIfExists('wallet_tranfers');
    }
}
