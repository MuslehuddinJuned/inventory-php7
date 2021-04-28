<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = ['current_pay_doller', 'basic_pay', 'medic_alw', 'fixed_allowance', 'house_rent', 'ta', 'da', 'other_field', 'other_pay', 'providant_fund', 'tax', 'total_salary', 'bank_name', 'acc_no', 'employee_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
