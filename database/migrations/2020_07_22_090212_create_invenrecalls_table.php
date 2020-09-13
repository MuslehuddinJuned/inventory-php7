<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvenrecallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invenrecalls', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->nullable();
            $table->float('master_sheet')->nullable();
            $table->float('price')->nullable();
            $table->date('receive_etd')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('inventoryreceive_id');
            $table->unsignedBigInteger('deleted_by')->default(0);

            $table->timestamps();
            $table->foreign('inventoryreceive_id')->references('id')->on('inventoryreceives')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invenrecalls');
    }
}
