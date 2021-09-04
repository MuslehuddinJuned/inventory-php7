<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_attendances', function (Blueprint $table) {
            $table->id();
            $table->date('att_date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->string('others')->nullable();
            $table->unsignedBigInteger('employee_id');
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
        Schema::dropIfExists('m_attendances');
    }
}
