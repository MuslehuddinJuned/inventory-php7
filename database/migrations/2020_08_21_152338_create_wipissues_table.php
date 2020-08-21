<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWipissuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wipissues', function (Blueprint $table) {
            $table->id();
            $table->float('issue_qty')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');

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
        Schema::dropIfExists('wipissues');
    }
}
