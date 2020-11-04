<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['employee_id', 'first_name', 'last_name', 'address', 'mobile_no', 'email', 'blood_group', 'gender', 
        'date_of_birth', 'marital_status', 'designation', 'department', 'section', 'work_location', 'start_date', 'effective_join_date', 
        'father_name', 'district', 'sub_district', 'zip_code', 'area', 'qualification', 'epf_entitled_in', 'team_member_of', 'transferred', 
        'contact_name', 'contact_address', 'contact_phone', 'relationship', 'employee_image', 'status', 'weekly_holiday', 'start_time', 
        'end_time', 'exit_type', 'reason', 'resign_date', 'effective_date'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
