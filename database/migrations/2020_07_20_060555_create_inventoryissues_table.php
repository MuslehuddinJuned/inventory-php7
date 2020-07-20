<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryissuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { //  ActualCreatedDate
        Schema::create('inventoryissues', function (Blueprint $table) {
            $table->id();
            $table->string('quantity')->nullable();
            $table->string('remarks')->nullable();
            $table->string('issue_price')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('inventory_id');

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
        Schema::dropIfExists('inventoryissues');
    }
}
