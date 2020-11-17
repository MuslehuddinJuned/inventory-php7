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
            $table->string('lot')->nullable();
            $table->string('container')->nullable();
            $table->double('quantity')->nullable();
            $table->double('ctn')->nullable();
            $table->double('pcs_per_ctn')->nullable();
            $table->double('cbm')->nullable();
            $table->string('shipment_booking')->nullable();
            $table->string('remarks')->nullable();
            $table->date('po_date')->nullable();
            $table->date('loading_date')->nullable();
            $table->date('inspection_date')->nullable();
            $table->date('etd')->nullable();
            $table->string('po_no')->nullable();
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
        Schema::dropIfExists('polists');
    }
}
