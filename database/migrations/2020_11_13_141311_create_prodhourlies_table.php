<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdhourliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prodhourlies', function (Blueprint $table) {
            $table->id();
            $table->string('line')->nullable();
            $table->string('section')->nullable();
            $table->string('remarks')->nullable();
            $table->date('prod_date')->nullable();
            $table->double('qty_1')->nullable();
            $table->double('ng_1')->nullable();
            $table->double('qty_2')->nullable();
            $table->double('ng_2')->nullable();
            $table->double('qty_3')->nullable();
            $table->double('ng_3')->nullable();
            $table->double('qty_4')->nullable();
            $table->double('ng_4')->nullable();
            $table->double('qty_5')->nullable();
            $table->double('ng_5')->nullable();
            $table->double('qty_6')->nullable();
            $table->double('ng_6')->nullable();
            $table->double('qty_7')->nullable();
            $table->double('ng_7')->nullable();
            $table->double('qty_8')->nullable();
            $table->double('ng_8')->nullable();
            $table->double('qty_9')->nullable();
            $table->double('ng_9')->nullable();
            $table->double('qty_10')->nullable();
            $table->double('ng_10')->nullable();
            $table->double('qty_11')->nullable();
            $table->double('ng_11')->nullable();
            $table->double('qty_12')->nullable();
            $table->double('ng_12')->nullable();
            $table->double('qty_13')->nullable();
            $table->double('ng_13')->nullable();
            $table->double('qty_14')->nullable();
            $table->double('ng_14')->nullable();
            $table->double('qty_15')->nullable();
            $table->double('ng_15')->nullable();
            $table->double('qty_16')->nullable();
            $table->double('ng_16')->nullable();
            $table->double('qty_17')->nullable();
            $table->double('ng_17')->nullable();
            $table->double('qty_18')->nullable();
            $table->double('ng_18')->nullable();
            $table->double('qty_19')->nullable();
            $table->double('ng_19')->nullable();
            $table->double('qty_20')->nullable();
            $table->double('ng_20')->nullable();
            $table->double('qty_21')->nullable();
            $table->double('ng_21')->nullable();
            $table->double('qty_22')->nullable();
            $table->double('ng_22')->nullable();
            $table->double('qty_23')->nullable();
            $table->double('ng_23')->nullable();
            $table->double('qty_24')->nullable();
            $table->double('ng_24')->nullable();
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
        Schema::dropIfExists('prodhourlies');
    }
}
