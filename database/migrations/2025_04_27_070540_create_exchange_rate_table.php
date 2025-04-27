<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rate', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_currency_id');
            $table->foreign('from_currency_id')->references('id')->on('currencies');
            $table->unsignedBigInteger('to_currency_id');
            $table->foreign('to_currency_id')->references('id')->on('currencies');
            $table->decimal('rate', 30, 16);
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
        Schema::dropIfExists('exchange_rate');
    }
}
