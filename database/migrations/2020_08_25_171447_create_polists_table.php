<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polists', function (Blueprint $table) {
            $table->id();
            $table->float('quantity')->nullable();
            $table->string('remarks')->nullable();
            $table->date('po_date')->nullable();
            $table->string('po_no')->nullable();            
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('producthead_id');
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
        Schema::dropIfExists('polists');
    }
}