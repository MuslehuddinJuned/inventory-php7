<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = ['year', 'casual_leave', 'sick_leave', 'annual_leave', 'maternity_leave', 'paternity_leave', 'compensatory_leave', 'unpaid_leave', 'half_leave'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
