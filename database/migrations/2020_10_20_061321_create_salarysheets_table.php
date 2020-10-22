<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarysheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salarysheets', function (Blueprint $table) {
            $table->id();
            $table->integer('checked')->nullable();
            $table->string('no_fo_days')->nullable();
            $table->string('year_mnth')->nullable();
            $table->double('basic_daily')->nullable();
            $table->double('basic_monthly')->nullable();
            $table->double('house_rent')->nullable();
            $table->double('medic_allowance')->nullable();
            $table->double('salary')->nullable();
            $table->double('covert_rate')->nullable();
            $table->double('salary_usd')->nullable();
            $table->double('ta')->nullable();
            $table->double('da')->nullable();
            $table->double('attendance_bonus')->nullable();
            $table->double('production_bonus')->nullable();
            $table->double('worked_friday_hour')->nullable();
            $table->double('worked_friday_amount')->nullable();
            $table->double('worked_holiday_hour')->nullable();
            $table->double('worked_holiday_amount')->nullable();
            $table->double('ot_rate')->nullable();
            $table->double('ot_hour')->nullable();
            $table->double('ot_amount')->nullable();
            $table->double('fixed_allowance')->nullable();
            $table->double('attendance_allowance')->nullable();
            $table->double('present_days')->nullable();
            $table->double('holidays')->nullable();
            $table->double('absent_days')->nullable();
            $table->double('absent_amount')->nullable();
            $table->double('leave_days')->nullable();
            $table->double('advance')->nullable();
            $table->double('pf')->nullable();
            $table->double('tax')->nullable();
            $table->double('deducted')->nullable();
            $table->double('not_for_join_days')->nullable();
            $table->double('not_for_join_amount')->nullable();
            $table->double('lay_off_days')->nullable();
            $table->double('lay_off_amount')->nullable();
            $table->double('suspense_days')->nullable();
            $table->double('suspense_amount')->nullable();
            $table->double('gross_pay')->nullable();
            $table->double('total_deduction')->nullable();
            $table->double('net_pay')->nullable();

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
        Schema::dropIfExists('salarysheets');
    }
}
