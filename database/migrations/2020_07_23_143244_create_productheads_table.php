<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductheadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productheads', function (Blueprint $table) {
            $table->id();
            $table->string('product_category')->nullable();
            $table->string('buyer')->nullable();
            $table->string('product_style')->nullable();
            $table->string('product_code')->nullable();
            $table->string('specification')->nullable();
            $table->double('smv')->nullable();
            $table->double('manpower')->nullable();
            $table->string('product_image')->default('noimage.jpg');
            $table->string('remarks')->nullable();
            
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('productheads');
    }
}
