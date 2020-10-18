<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWagehikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wagehikes', function (Blueprint $table) {
            $table->id();
            $table->date('effective_date')->nullable();
            $table->date('next_increment')->nullable();
            $table->double('amount')->nullable();
            $table->string('remarks')->nullable();
            $table->string('file_link')->nullable();

            $table->unsignedBigInteger('employee_id')->nullable();
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
        Schema::dropIfExists('wagehikes');
    }
}
