<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('year')->nullable();
            $table->integer('casual_leave')->nullable();
            $table->integer('sick_leave')->nullable();
            $table->integer('annual_leave')->nullable();
            $table->integer('maternity_leave')->nullable();
            $table->integer('paternity_leave')->nullable();
            $table->integer('compensatory_leave')->nullable();
            $table->integer('unpaid_leave')->nullable();
            $table->integer('half_leave')->nullable();
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
        Schema::dropIfExists('leaves');
    }
}
