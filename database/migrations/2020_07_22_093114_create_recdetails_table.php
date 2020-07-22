<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recdetails', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('accept')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('rechead_id');

            $table->timestamps();
            $table->foreign('rechead_id')->references('id')->on('recheads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recdetails');
    }
}
