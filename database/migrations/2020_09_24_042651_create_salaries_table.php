<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->double('current_pay_doller')->nullable();
            $table->double('basic_pay')->nullable();
            $table->double('medic_alw')->nullable();
            $table->double('house_rent')->nullable();
            $table->double('ta')->nullable();
            $table->double('da')->nullable();
            $table->string('other_field')->nullable();
            $table->double('other_pay')->nullable();
            $table->double('providant_fund')->nullable();
            $table->double('tax')->nullable();
            $table->double('total_salary')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('acc_no')->nullable();

            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
