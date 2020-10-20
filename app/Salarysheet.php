<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salarysheet extends Model
{
    protected $fillable = ['year_month', 'next_increment', 'basic_monthly', 'house_rent', 'medic_allowance', 'salary', 'covert_rate', 
        'ta', 'da', 'attendance_bonus', 'production_bonus', 'worked_friday_hour', 'worked_friday_amount', 'worked_holiday_hour', 
        'worked_holiday_amount', 'ot_rate', 'ot_hour', 'ot_amount', 'fixed_allowance', 'attendance_allowance', 'present_days', 
        'absent_days', 'leave_days', 'advance', 'pf', 'tax', 'deducted', 'not_for_join_days', 'not_for_join_amount', 'lay_off_days', 
        'lay_off_amount', 'suspense_days', 'suspense_amount', 'employee_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
