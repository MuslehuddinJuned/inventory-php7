<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWipreceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wipreceives', function (Blueprint $table) {
            $table->id();
            $table->double('receive_qty')->nullable();
            $table->string('etd')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('producthead_id');
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
        Schema::dropIfExists('wipreceives');
    }
}
