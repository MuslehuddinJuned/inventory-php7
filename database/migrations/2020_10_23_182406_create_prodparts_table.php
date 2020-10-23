<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdpartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prodparts', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('producthead_id');
            $table->unsignedBigInteger('subpart_id');
            $table->unsignedBigInteger('polist_id');
            $table->unsignedBigInteger('prodstore_id');
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
        Schema::dropIfExists('prodparts');
    }
}
