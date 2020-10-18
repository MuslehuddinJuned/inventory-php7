<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('ac_no')->nullable();
            $table->string('name')->nullable();
            $table->string('department')->nullable();
            $table->date('date')->nullable();
            $table->string('time')->nullable();
            $table->time('in_time_1')->nullable();
            $table->time('in_time_2')->nullable();
            $table->time('out_time_1')->nullable();
            $table->time('out_time_2')->nullable();
            $table->double('ot')->nullable();
            $table->double('ot_extra')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('attendances');
    }
}
