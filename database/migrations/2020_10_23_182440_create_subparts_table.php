<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubpartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subparts', function (Blueprint $table) {
            $table->id();
            $table->string('parts_name')->nullable();
            $table->string('parts_description')->nullable();
            $table->double('parts_qty')->nullable();
            $table->string('unit')->nullable();
            $table->text('remarks')->nullable();
            $table->string('parts_image')->default('noimage.jpg');
            
            $table->unsignedBigInteger('producthead_id')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('subparts');
    }
}
