<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryreceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::create('inventoryreceives', function (Blueprint $table) {
            $table->id();
            $table->string('remarks')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('challan_no')->nullable();
            $table->string('storeReceive_id')->nullable();
            $table->string('stock_type')->nullable();
            $table->date('challan_date')->nullable();
            $table->date('receive_date')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('polist_id')->nullable();
            $table->unsignedBigInteger('deleted_by')->default(0);

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
        Schema::dropIfExists('inventoryreceives');
    }
}
