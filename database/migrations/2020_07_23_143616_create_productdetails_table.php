<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productdetails', function (Blueprint $table) {
            $table->id();
            $table->string('quantity')->nullable();
            $table->string('remarks')->nullable();
            
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('producthead_id');
            $table->unsignedBigInteger('inventory_id');
            $table->timestamps();
            
            $table->foreign('producthead_id')->references('id')->on('productheads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productdetails');
    }
}
