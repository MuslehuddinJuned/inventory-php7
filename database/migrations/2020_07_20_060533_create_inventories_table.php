<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * 
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item')->nullable();
            $table->string('item_code')->nullable();
            $table->string('specification')->nullable();
            $table->string('unit')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('item_image')->default('noimage.jpg');
            $table->double('cann_per_sheet')->nullable();
            $table->double('weight')->nullable();
            $table->string('grade')->nullable();
            $table->string('accounts_code')->nullable();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deleted_by')->default(0);

            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
}
