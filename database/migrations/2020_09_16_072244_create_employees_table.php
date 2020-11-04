<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('address')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->string('section')->nullable();
            $table->string('work_location')->nullable();
            $table->date('start_date')->nullable();
            // start for shun ho
            //pretend 'Type Of Employee' as 'Department' and 'Department' as 'Section'
            $table->date('effective_join_date')->nullable();
            $table->string('father_name')->nullable();
            $table->string('district')->nullable();
            $table->string('sub_district')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('area')->nullable();
            $table->string('qualification')->nullable();
            $table->string('epf_entitled_in')->nullable();
            $table->string('team_member_of')->nullable();
            $table->string('transferred')->nullable();
            // end for shun ho
            $table->string('contact_name')->nullable();
            $table->text('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('relationship')->nullable();
            $table->string('employee_image')->default('noimage.jpg');
            $table->string('status')->default('active');
            $table->string('weekly_holiday')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('exit_type')->nullable();
            $table->string('reason')->nullable();
            $table->date('resign_date')->nullable();
            $table->date('effective_date')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
