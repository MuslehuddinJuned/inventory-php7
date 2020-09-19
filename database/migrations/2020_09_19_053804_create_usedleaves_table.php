<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsedleavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usedleaves', function (Blueprint $table) {
            $table->id();
            $table->string('leave_type')->nullable();
            $table->string('reason')->nullable();
            $table->string('replacing_person')->nullable();
            $table->date('leave_start')->nullable();
            $table->date('leave_end')->nullable();
            $table->integer('day_count')->nullable();
            $table->unsignedBigInteger('employee_id');
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
        Schema::dropIfExists('usedleaves');
    }
}
