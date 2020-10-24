<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProditemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proditems', function (Blueprint $table) {
            $table->id();
            $table->double('prod_qty')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('prodstore_id')->nullable();
            $table->unsignedBigInteger('from_channel_id')->nullable();
            $table->unsignedBigInteger('to_channel_id')->nullable();
            $table->unsignedBigInteger('polist_id');
            $table->unsignedBigInteger('producthead_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('proditems');
    }
}
