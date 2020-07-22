<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryreceivesdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventoryreceivesdetails', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->nullable();
            $table->float('price')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('inventoryreceive_id');

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
        Schema::dropIfExists('inventoryreceivesdetails');
    }
}
