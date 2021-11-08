<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableWager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wager' , function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('total_wager_value');
            $table->tinyInteger('odds');
            $table->tinyInteger('selling_percentage');
            $table->decimal('selling_price' , 14 , 2);
            $table->decimal('current_selling_price' , 14 , 2);
            $table->tinyInteger('percentage_sold')->nullable();
            $table->decimal('amount_sold' , 14 , 2);
            $table->dateTime('placed_at' , 0);
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
        Schema::dropIfExists('wager');
    }
}
